<?php

namespace Qsoft\Committees\Install;

use Bitrix\Main\Localization\Loc;

class EventTypeInstaller
{
    public static function install(array $eventType): bool
    {
        $isInstalled = self::checkIfExists($eventType['EVENT_NAME']);

        if ($isInstalled === false && \CEventType::Add($eventType) === false) {
            global $APPLICATION;

            $APPLICATION->ThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_INSTALL_EVENT_TYPE_INSTALL_ERROR', ['#EVENT_NAME#' => $eventType['EVENT_NAME']]
            ));

            return false;
        }

        return true;
    }

    public static function uninstall(array $eventType): bool
    {
        $isInstalled = self::checkIfExists($eventType['EVENT_NAME']);

        if ($isInstalled === true && \CEventType::Delete($eventType['EVENT_NAME']) === false) {
            global $APPLICATION;

            $APPLICATION->ThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_INSTALL_EVENT_TYPE_DELETE_ERROR', ['#EVENT_NAME#' => $eventType['EVENT_NAME']]
            ));
            return false;
        }

        return true;
    }

    public static function checkIfExists(string $eventName): bool
    {
        return \CEventType::GetList([
            'EVENT_NAME' => $eventName
        ])->Fetch() !== false;
    }

    public static function getRequiredEventTypes(): array
    {
        return
            [
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_ADD',
                    'NAME' => 'Календарь: новое событие',
                    'EVENT_TYPE' => 'email',
                    'LID' => 'ru',
                    'DESCRIPTION' =>
                        "#EMAIL_TO# - EMail получателей сообщения" . PHP_EOL
                        . "#EVENT_NAME# - Название события" . PHP_EOL
                        . "#DATE_FROM# - Дата начала события" . PHP_EOL
                        . "#DATE_TO# - Дата окончания события"
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_UPDATE',
                    'NAME' => 'Календарь: изменение события',
                    'EVENT_TYPE' => 'email',
                    'LID' => 'ru',
                    'DESCRIPTION' =>
                        "#EMAIL_TO# - EMail получателей сообщения" . PHP_EOL
                        . "#EVENT_NAME# - Название события" . PHP_EOL
                        . "#DATE_FROM# - Дата начала события" . PHP_EOL
                        . "#DATE_TO# - Дата окончания события"
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_DELETE',
                    'NAME' => 'Календарь: отмена события',
                    'EVENT_TYPE' => 'email',
                    'LID' => 'ru',
                    'DESCRIPTION' =>
                        "#EMAIL_TO# - EMail получателей сообщения" . PHP_EOL
                        . "#EVENT_NAME# - Название события" . PHP_EOL
                        . "#DATE_FROM# - Дата начала события" . PHP_EOL
                        . "#DATE_TO# - Дата окончания события"
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_INTERSECTION',
                    'NAME' => 'Календарь: пересечение событий',
                    'EVENT_TYPE' => 'email',
                    'LID' => 'ru',
                    'DESCRIPTION' => '#MESSAGE# - текст сообщения' . PHP_EOL
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_USER_EXCLUDED',
                    'NAME' => 'Календарь: исключение из события',
                    'EVENT_TYPE' => 'email',
                    'LID' => 'ru',
                    'DESCRIPTION' =>
                        "#EMAIL_TO# - EMail получателей сообщения" . PHP_EOL
                        . "#EVENT_NAME# - Название события" . PHP_EOL
                        . "#DATE_FROM# - Дата начала события" . PHP_EOL
                        . "#DATE_TO# - Дата окончания события"
                ]
            ];
    }
}