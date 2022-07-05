<?php
namespace Qsoft\Committees\Service;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\Collection;
use Qsoft\Committees\Admin\Options;
use Qsoft\Committees\Entity\User;
use Qsoft\Committees\Logger\Logger;
use Qsoft\Committees\Orm\EventTable;
use Bitrix\Main\Config\Option;

use const Qsoft\Committees\MODULE_ID;

class Intersections
{
    private const MAX_SIZE = 100; //максимальное количество событий, которые будут обработаны за один запуск
    private \Monolog\Logger $logger;

    public static function exec(): string
    {
        $logger = Logger::get('intersections');
        try {
            $self = new self($logger);
            $self->check();

        } catch (\Exception | \Throwable $e) {
            $logger->critical($e->getMessage());
        }

        return self::getAgentMethod();
    }

    public function __construct(\Monolog\Logger $logger)
    {
        $this->logger = $logger;

        if (!Loader::includeModule('calendar')) {
            throw new LoaderException('Module "calendar" is not installed');
        }

        if (!Loader::includeModule('im')) {
            throw new LoaderException('Module "im" is not installed');
        }
    }

    /**
     * поиск пересечений для всех новых событий, если находит, то отправляем уведомления участникам
     * уведомление отправляется только OWNER_ID, повторяющиеся события и события на всю компанию, группы, подразделения и т.д. не учитываются
     * функция вызывается по агенту, раз в 1 минуту
     */
    public function check(): void
    {
        $events = $this->getEvents();
        $intersections = $this->find($events);
        $this->sendNotifications($intersections);
    }

    /**
     * Проверка на перечение конкретного события по его id.
     * Нужна если в будущем потрбуется сделать проверку сразу при созднии события и не ждать агента
     * @param int $id
     * @param bool $sendNotify
     * @return bool
     */
    public function checkById(int $id, bool $sendNotify = true): bool
    {
        $intersections = $this->find(['ID' => $id]);

        if ($sendNotify) {
            $this->sendNotifications($intersections);
        }

        return !empty($intersections);
    }

    /**
     * Поиск всех пересекающихся событий для переданного массива событий
     * @param array $events
     * @return array
     */
    private function find(array $events): array
    {
        $items = [];

        foreach ($events as $event) {
            $this->markAsChecked($event['ID']);

            $intersections = $this->findIntersections($event);

            if (empty($intersections)) {
                $this->logger->info(
                    Loc::getMessage('QSOFT_COMMITTEES_SERVICE_INTERSECIONS_EMPTY'),
                    ['event' => $event]
                );
                continue;
            } else {
                $this->logger->info(
                    Loc::getMessage('QSOFT_COMMITTEES_SERVICE_INTERSECIONS_FIND'),
                    ['event' => $event, 'intersections' => $intersections]
                );
            }

            $ownerId = $event['OWNER_ID'];

            $items[$ownerId] = [
                $event['ID'] => $event
            ];

            foreach ($intersections as $intersection) {
                $items[$ownerId][$intersection['ID']] = $intersection;
            }
        }

        return $items;
    }

    /**
     * Отправка уведомлений о пересекающихся событиях
     * @param array $intersections
     */
    private function sendNotifications(array $intersections)
    {
        $emailFrom = Option::get('main', 'email_from');
        $userIds = array_keys($intersections);
        $ids = [];
        $notifOnlyActive = Options::getNotifOnlyActiveOption() === 'Y';

        if ($notifOnlyActive) {
            foreach ($userIds as $userId) {
                if (User::isActive($userId)) {
                    $ids[] = $userId;
                }
            }
        } else {
            $ids = $userIds;
        }

        $emails = User::getEmailByIds($ids);

        foreach ($intersections as $userId => $events) {
            if (in_array($userId, $ids)) {
                $message = $this->createNotificationMessage($userId, $events);

                $this->logger->info(
                    'Отправлено уведомление о пересечениях',
                    [
                        'user_id' => $userId,
                        'message' => $message
                    ]
                );

                $fields = [
                    'MESSAGE' => $message,
                    'EMAIL_FROM' => $emailFrom,
                    'EMAIL_TO' => $emails[$userId]
                ];

                Event::send(
                    [
                        "EVENT_NAME" => 'COMMITTEES_EVENT_INTERSECTION',
                        "LID" => "s1",
                        "C_FIELDS" => $fields
                    ]
                );
            }
        }
    }

    /**
     * Генерация сообщения для уведомления
     *
     * @param int $userId
     * @param array $events
     * @return string
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function createNotificationMessage(int $userId, array $events): string
    {
        $pathToCalendar = \CCalendar::GetPathForCalendarEx($userId);
        $pathToCalendar = \CHTTP::urlDeleteParams($pathToCalendar, ['action', 'sessid', 'bx_event_calendar_request', 'EVENT_ID']);

        $rows = [];

        Collection::sortByColumn(
            $events,
            [
                'DATE_FROM_TS_UTC' => SORT_ASC,
                'ID' => SORT_ASC
            ]
        );

        foreach ($events as $event) {
            $event['URL'] = \CHTTP::urlAddParams($pathToCalendar, ['EVENT_ID' => $event['ID']]);
            $rows[] = Loc::getMessage('QSOFT_COMMITTEES_SERVICE_NOTIFY_EVENT', $event);
        }

        return Loc::getMessage(
            'QSOFT_COMMITTEES_SERVICE_NOTIFY',
            [
                '#FIRST_EVENT_NAME#' => $events[0]['NAME'],
                '#FIRST_EVENT_DATE_FROM#' => $events[0]['DATE_FROM'],
                '#SECOND_EVENT_NAME#' => $events[1]['NAME']
            ]
        );
    }

    /**
     * Поиск всех событий, которые требуют проверку
     *
     * @param array $filter
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getEvents(array $filter = []): array
    {
        return EventTable::getList(
            [
                'filter' => array_merge(
                    $this->getFilter(),
                    $filter,
                    [
                        '=UF_INTERSECTIONS_CHECKED' => [null, 0], //требуют проверку пересечений
                        '>=DATE_FROM' => new DateTime() //еще не наступили
                    ],

                ),
                'select' => $this->getSelect(),
                'limit' => self::MAX_SIZE
            ]
        )->fetchAll();
    }

    /**
     * Помечает событие, как проверенное
     * @param int $id
     */
    private function markAsChecked(int $id): void
    {
        \CCalendarEvent::UpdateUserFields($id, ['UF_INTERSECTIONS_CHECKED' => 1]);
    }

    /**
     * Поиск событий пользователя, которые пересекаются с его событием $arEvent
     *
     * @param array $arEvent
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function findIntersections(array $arEvent): array
    {
        return EventTable::getList(
            [
                'filter' => array_merge(
                    $this->getFilter(),
                    [
                        '!=ID' => $arEvent['ID'],
                        '=OWNER_ID' => $arEvent['OWNER_ID'],
                        '>DATE_TO' => $arEvent['DATE_FROM'],
                        '<DATE_FROM' => $arEvent['DATE_TO'],
                    ]
                ),
                'select' => $this->getSelect()
            ]
        )->fetchAll();
    }

    /**
     * Фильтр по умолчанию
     *
     * @return array|string[]
     */
    private function getFilter(): array
    {
        return [
            '=DELETED' => 'N',
            '=ACTIVE' => 'Y',
        ];
    }

    /**
     * Поля событий по умолчанию
     *
     * @return array|string[]
     */
    private function getSelect(): array
    {
        return ['ID', 'OWNER_ID', 'NAME', 'DATE_FROM', 'DATE_TO', 'DATE_FROM_TS_UTC', 'UF_INTERSECTIONS_CHECKED'];
    }

    public static function activate(): void
    {
        self::deactivate(); //на всякий случай удаляем старый агент, если он есть
        \CAgent::AddAgent(self::getAgentMethod(), MODULE_ID, 'N', 60);
    }

    public static function deactivate(): void
    {
        \CAgent::RemoveAgent(self::getAgentMethod(), MODULE_ID);
    }

    private static function getAgentMethod()
    {
        return '\\' . __CLASS__ . '::exec();';
    }
}
