<?php

namespace Qsoft\Committees\Api;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Qsoft\Committees\Admin\Options;
use Qsoft\Committees\Date\DateTime;
use Qsoft\Committees\Exception\CommitteesException;
use Qsoft\Committees\Handlers\Socialnetwork;
use Qsoft\Committees\Meeting\Host;
use Qsoft\Committees\Orm\EventTable;

final class Events
{
    private const CREATED = 0;
    private const CHANGED = 1;
    private const DELETED = 2;
    private const EVENTS_COLOR = '#015901';

    private array $meta;
    private static \Monolog\Logger $logger;

    private int $pageNum;
    private int $perPage;

    private string $beginDateString;
    private string $endDateString;
    private int $beginDateTimestamp;
    private int $endDateTimestamp;

    private array $events;
    private array $curEvent;

    public function __construct(\Monolog\Logger $logger)
    {
        $this->setDates();
        $this->setLogger($logger);
        $this->setPerPage();
        $this->setPageNum(Options::getOption('page_number'));

        $this->beginDateTimestamp = DateTime::getTimestampFromString($this->beginDateString);
        $this->endDateTimestamp = DateTime::getTimestampFromString($this->endDateString);
    }

    /**
     * Устанавливает значения свойств дат (строковые и таймстампы)
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function setDates(): void
    {
        $this->beginDateString = Options::getOption('upload_date_start');
        $this->endDateString = Options::getOption('upload_date_end');

        $this->validateDates();

        $this->beginDateTimestamp = DateTime::getTimestampFromString($this->beginDateString);
        $this->endDateTimestamp = DateTime::getTimestampFromString($this->endDateString);
    }

    /**
     * Производит проверку на корректность формата даты
     *
     * @param string $date
     * @throws CommitteesException
     */
    private function validateDate(string $date): void
    {
        if ($date !== '' && !DateTime::validate($date)) {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_WRONG_DATE_FORMAT',
                ['#DATE#' => $date]
            ));
        }
    }

    /**
     * Производит проверку на корректность формата введенных в настройках дат
     *
     * @throws CommitteesException
     */
    private function validateDates(): void
    {
        $this->validateDate($this->beginDateString);
        $this->validateDate($this->endDateString);
    }

    /**
     * Выгружает данные текущей страницы
     *
     * @throws CommitteesException
     */
    public function uploadPage(): void
    {
        $this->getLogger()->info(Loc::getMessage(
            'QSOFT_COMMITTEES_LOG_PAGE_UPLOAD_START',
            ['#PAGE_NUM#' => $this->getPageNum()]
        ));

        $this->events = $this->getData();

        if (!$this->isSuccess()) {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_HTTP_ERROR',
                ['#ERROR_MESSAGE#' => $this->events['message'], '#HTTP_CODE#' => $this->events['status']]
            ));
        }

        $this->setMeta();

        if ($this->getPageCount() < $this->getPageNum()) {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_PAGE_COUNT_ZERO',
                ['#PAGE_COUNT#' => $this->getPageCount()]
            ));
        }

        $this->saveCurPageData();

        $this->getLogger()->info(Loc::getMessage(
            'QSOFT_COMMITTEES_LOG_PAGE_UPLOADED',
            ['#PAGE_NUM#' => $this->getPageNum()]
        ));
    }

    /**
     * Удаляет событие
     *
     * @throws CommitteesException
     */
    private function deleteEvent(): void
    {
        $elemId = $this->getCurEventIdIfExists();

        if ($elemId > 0) {
            $result = \CCalendar::DeleteEvent(
                $elemId,
                false,
                [
                    'checkPermissions' => false,
                    'sendNotification' => true
                ]
            );

            if ($result !== true) {
                $this->logAndThrowException(Loc::getMessage(
                    'QSOFT_COMMITTEES_EXCEPTION_ELEMENT_DELETE_ERROR',
                    ['#ELEMENT_ID#' => $elemId, '#ERROR_MSG#' => $result]
                ));
            } else {
                \CCalendarEvent::UpdateUserFields($elemId, ['UF_COMMITTEE_EVENT_ID' => null]);
            }
        } else {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_ELEMENT_DELETE_ERROR_NO_ELEMENT'
            ));
        }
    }

    /**
     * Сохраняет/обновляет информацию об одном событии
     *
     * @param array $arEventFields
     * @throws CommitteesException
     */
    private function saveEvent(array $arEventFields): void
    {
        $result = $this->saveEventInCalendar($arEventFields);

        if (is_int($result) && $result > 0) {
            if ($arEventFields['ID'] === 0) {
                \CCalendarEvent::UpdateUserFields($result, ['UF_COMMITTEE_EVENT_ID' => $this->getCommitteeId()]);
            }
        } else {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_ELEMENT_ADD_ERROR',
                ['#ELEMENT_ID#' => $this->getCommitteeId(), '#ERROR_MSG#' => $result]
            ));
        }
    }

    /**
     * Сохраняет данные события в календарь
     *
     * @param $event
     * @throws CommitteesException
     */
    public function uploadEventData($event): void
    {
        $this->curEvent = $event;
        $arEventFields = $this->makeFieldsArray();
        $eventStatus = $event['status'] ?? self::CREATED;
        $this->getLogger()->info(json_encode($arEventFields));
        if ($eventStatus === self::DELETED) {
            $this->deleteEvent();
        } elseif ($eventStatus === self::CREATED || $eventStatus === self::CHANGED) {
            $this->saveEvent($arEventFields);
        } else {
            $this->logAndThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_EXCEPTION_WRONG_STATUS',
                ['#STATUS#' => $eventStatus]
            ));
        }
    }

    /**
     * Сохраняет данные текущей страницы событий в календарь
     *
     * @throws CommitteesException
     */
    private function saveCurPageData(): void
    {
        global $DB;

        $DB->StartTransaction();

        try {

            foreach ($this->events['data'] as $event) {
                $this->uploadEventData($event);
            }

            $DB->Commit();

        } catch (\Exception $e) {
            $DB->Rollback();

            $this->logAndThrowException($e->getMessage());
        }

    }

    private function logAndThrowException(string $message): void
    {
        $this->getLogger()->critical($message);
        throw new CommitteesException($message);
    }

    /**
     * Сохраняет данные события в календарь
     *
     * @param $arEventFields
     * @return array|array[]|bool|bool[]|mixed|string|void
     */
    function saveEventInCalendar($arEventFields)
    {
        Socialnetwork::disableLog();
        return \CCalendar::SaveEvent([
            'arFields' => $arEventFields,
            'sendEditNotification' => false,
            'checkPermission' => false,
            'sendInvitations' => false
        ]);
    }

    /**
     * Делает запрос в МС и возвращает полученные данные
     *
     * @return array
     * @throws CommitteesException
     */
    public function getData(): array
    {
        $request = new Request($this);
        $response = $request->getResponse();

        if ($response->getStatus() === ResponseStatus::STATUS_ERROR) {
            $errorMessage = Loc::getMessage('QSOFT_COMMITTEES_EXCEPTION_API_ERROR');

            $this->getLogger()->info($errorMessage);
            throw new CommitteesException($errorMessage, 503);
        }

        return $response->getResponseBody();
    }

    /**
     * Формирует массив для сохранения события по данным запроса
     *
     * @return array
     */
    private function makeFieldsArray(): array
    {
        $attendeesIds = $this->getAttendeesIds();
        $attendeesCodes = $this->formatIdsToCode($attendeesIds);
        $id = $this->getCurEventIdIfExists();
        $hostInfo = Host::getInfo(['NAME', 'ID']);
        $timezoneName = DateTime::getTimezoneName();

        $beginDate = DateTime::getStringFromTimestamp($this->curEvent['begin_date']);
        $endDate = DateTime::getStringFromTimestamp($this->curEvent['end_date']);

        return [
            "ID" => $id,
            "DATE_FROM" => $beginDate,
            "DATE_TO" => $endDate,
            "TZ_FROM" => $timezoneName,
            "TZ_TO" => $timezoneName,
            "NAME" => $this->getName(),
            "OWNER_ID" => $hostInfo['ID'],
            "DESCRIPTION" => "",
            "IS_MEETING" => true,
            "SECTION_CAL_TYPE" => "user",
            "SECTION_OWNER_ID" => (string)$hostInfo['ID'],
            "ATTENDEES_CODES" => $attendeesCodes,
            "ATTENDEES" => $attendeesIds,
            "MEETING_HOST" => $hostInfo['ID'],
            "COLOR" => self::EVENTS_COLOR,
            "MEETING" => [
                "HOST_NAME" => $hostInfo['NAME'],
                "NOTIFY" => false,
                "MEETING_CREATOR" => $hostInfo['ID'],
                "ROOMS" => $this->getRooms(),
                "EXTERNAL_LINK" => $this->getExternalLink()
            ],
            "UF_COMMITTEE_EVENT_ID" => $this->getCommitteeId(),
            "REMIND" => [
                ['type' => 'min', 'count' => 15]
            ]
        ];
    }

    /**
     * Формирует ссылку на событие в МС
     *
     * @return string|string[]
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function getExternalLink()
    {
        return str_replace('#event_id#', $this->getCommitteeId(), Options::getOption('link_template'));
    }

    /**
     * Возвращает массив кодов участников по массиву их id
     *
     * @param array $arIds
     * @return array
     */
    private function formatIdsToCode(array $arIds): array
    {
        return array_map(function($element) {
            return 'U' . $element;
        }, $arIds);
    }

    /**
     * Возвращает true, если запрос вернул статус 200
     *
     * @return bool
     */
    private function isSuccess(): bool
    {
        return (int)$this->events['status'] === 200;
    }

    /**
     * Возвращает id события в календаре, если оно уже существует. Если нет, возвращает 0
     *
     * @return int
     */
    private function getCurEventIdIfExists(): int
    {
        $id = 0;

        $arFilter = [
            'UF_COMMITTEE_EVENT_ID' => $this->getCommitteeId()
        ];

        $arSelect = [
            'ID',
            'UF_COMMITTEE_EVENT_ID'
        ];

        $curEvent = EventTable::getList([
            'filter' => $arFilter,
            'select' => $arSelect,
            'limit' => 1
        ])->fetch();

        if ($curEvent) {
            $id = $curEvent['ID'];
        }

        return $id;
    }

    /**
     * Возвращает список id участников события
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getAttendeesIds(): array
    {
        $attendeesIds = [];
        $attendeesLogins = $this->getUsersLogin();
        $attendeesLogins[] = Host::getLogin();

        if (!empty($attendeesLogins)) {
            $filter = [
                '@LOGIN' => $attendeesLogins
            ];

            $rsAttendeesIds = UserTable::getList([
                'filter' => $filter,
                'select' => ['ID']
            ]);

            while ($attendeeId = $rsAttendeesIds->fetch()) {
                $attendeesIds[] = $attendeeId['ID'];
            }
        }

        return $attendeesIds;
    }

    /**
     * Устанавливает логгер в свойство
     *
     * @param $logger
     */
    private function setLogger($logger): void
    {
        if (!isset(self::$logger)) {
            self::$logger = $logger;
        }
    }

    /**
     * Устанавливает значение свойства "количество на странице"
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function setPerPage(): void
    {
        $this->perPage = Options::getOption('count_on_page');
    }

    /**
     * Возвращает количество событий на странице
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Возвращает логгер
     *
     * @return \Monolog\Logger
     */
    public function getLogger(): \Monolog\Logger
    {
        return self::$logger;
    }

    /**
     * Устанавливает значение свойства "номер текущей страницы"
     *
     * @param $pageNum
     */
    private function setPageNum($pageNum): void
    {
        $this->pageNum = $pageNum;
    }

    /**
     * Возвращает номер текущей страницы
     *
     * @return int
     */
    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    /**
     * Возвращает таймстамп ранней даты последнего обновления события
     *
     * @return int
     */
    public function getBeginDateTimestamp(): int
    {
        return $this->beginDateTimestamp;
    }

    /**
     * Возвращает таймстамп поздней даты последнего обновления события
     *
     * @return int
     */
    public function getEndDateTimestamp(): int
    {
        return $this->endDateTimestamp;
    }

    /**
     * Устанавливает значение свойства "метаданные"
     *
     */
    private function setMeta(): void
    {
        $this->meta = $this->events['_meta'];
    }

    /**
     * Возвращает массив метаданных
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Возвращает массив логинов участников события
     *
     * @return array
     */
    private function getUsersLogin(): array
    {
        return $this->curEvent['users_login'];
    }

    /**
     * Возвращает массив комнат текущего события
     *
     * @return array
     */
    private function getRooms(): array
    {
        return $this->curEvent['rooms'];
    }

    /**
     * Возвращает название события
     *
     * @return string
     */
    private function getName(): string
    {
        return $this->curEvent['name'];
    }

    /**
     * Возвращает id события из МС
     *
     * @return int
     */
    private function getCommitteeId(): int
    {
        return $this->curEvent['id'];
    }

    /**
     * Возвращает количество страниц запроса
     *
     * @return int
     */
    private function getPageCount(): int
    {
        return (int)$this->getMeta()['pageCount'];
    }
}
