<?php

namespace Qsoft\Committees\Install;

use Bitrix\Main\Localization\Loc;

class EventMessageInstaller
{
    protected static $cEventMessage = null;

    public static function install($eventMessage): bool
    {
        if (self::$cEventMessage === null) {
            self::$cEventMessage = new \CEventMessage();
        }

        $id = self::getIdIfExists($eventMessage['EVENT_NAME'], $eventMessage['EVENT_TYPE']);

        if ($id === 0 && self::$cEventMessage->Add($eventMessage) === false) {
            global $APPLICATION;

            $APPLICATION->ThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_INSTALL_EVENT_MESSAGE_INSTALL_ERROR', ['#EVENT_NAME#' => $eventMessage['EVENT_NAME']]
            ));

            return false;
        }

        return true;
    }

    public static function uninstall($eventMessage): bool
    {
        $id = self::getIdIfExists($eventMessage['EVENT_NAME'], $eventMessage['EVENT_TYPE']);

        if ($id !== 0 && \CEventMessage::Delete($id) === false) {
            global $APPLICATION;

            $APPLICATION->ThrowException(Loc::getMessage(
                'QSOFT_COMMITTEES_INSTALL_EVENT_MESSAGE_DELETE_ERROR', ['#EVENT_NAME#' => $eventMessage['EVENT_NAME']]
            ));

            return false;
        }

        return true;
    }

    public static function getIdIfExists(string $eventName, string $eventType = ''): int
    {
        $id = 0;

        $messageTemplate = \CEventMessage::GetList(
            ($by = 'id'),
            ($order = 'asc'),
            ['EVENT_NAME' => $eventName, 'EVENT_TYPE' => $eventType]
        )->Fetch();

        if ($messageTemplate) {
            $id = $messageTemplate['ID'];
        }

        return $id;
    }

    public static function getRequiredEventTemplates(): array
    {
        return
            [
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_ADD',
                    'LID' => ['s1'],
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => 'Календарь: новое событие',
                    'MESSAGE' => 'Уведомление о появлении нового события \'#EVENT_NAME#\' в вашем календаре, запланированного на #DATE_FROM#. <br><br> Организатор события: <a href="#MEETING_HOST_LINK#">#MEETING_HOST_NAME#</a><br><br> Просмотреть информацию о событии: <a href="#EVENT_LINK#">#EVENT_NAME#</a> <br><br> <hr> <br><br> <span style="color: grey">ЭТО СООБЩЕНИЕ СГЕНЕРИРОВАНО СИСТЕМОЙ АВТОМАТИЧЕСКИ! НЕ ОТВЕЧАЙТЕ НА ДАННОЕ СООБЩЕНИЕ!</span>',
                    'BODY_TYPE' => 'html',
                    'BCC' => '',
                    'REPLY_TO' => '',
                    'CC' => '',
                    'IN_REPLY_TO' => '',
                    'PRIORITY' => '',
                    'FIELD1_NAME' => '',
                    'FIELD1_VALUE' => '',
                    'FIELD2_NAME' => '',
                    'FIELD2_VALUE' => '',
                    'SITE_TEMPLATE_ID' => '',
                    'ADDITIONAL_FIELD' => [],
                    'LANGUAGE_ID' => 'ru',
                    'EVENT_TYPE' => '[ COMMITTEES_EVENT_ADD ] Календарь: новое событие',
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_UPDATE',
                    'LID' => ['s1'],
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => 'Календарь: изменение события',
                    'MESSAGE' => 'Уведомление об изменении события \'#EVENT_NAME#\' вашего календаря, запланированного на #DATE_FROM#. <br><br> Организатор события: <a href="#MEETING_HOST_LINK#">#MEETING_HOST_NAME#</a><br><br> Просмотреть информацию о событии: <a href="#EVENT_LINK#">#EVENT_NAME#</a> <br><br> <hr> <br><br> <span style="color: grey">ЭТО СООБЩЕНИЕ СГЕНЕРИРОВАНО СИСТЕМОЙ АВТОМАТИЧЕСКИ! НЕ ОТВЕЧАЙТЕ НА ДАННОЕ СООБЩЕНИЕ!</span>',
                    'BODY_TYPE' => 'html',
                    'BCC' => '',
                    'REPLY_TO' => '',
                    'CC' => '',
                    'IN_REPLY_TO' => '',
                    'PRIORITY' => '',
                    'FIELD1_NAME' => '',
                    'FIELD1_VALUE' => '',
                    'FIELD2_NAME' => '',
                    'FIELD2_VALUE' => '',
                    'SITE_TEMPLATE_ID' => '',
                    'ADDITIONAL_FIELD' => [],
                    'LANGUAGE_ID' => 'ru',
                    'EVENT_TYPE' => '[ COMMITTEES_EVENT_UPDATE ] Календарь: изменение события',
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_DELETE',
                    'LID' => ['s1'],
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => 'Календарь: отмена события',
                    'MESSAGE' => 'Уведомление об отмене события \'#EVENT_NAME#\' вашего календаря, запланированного на #DATE_FROM#. <br><br> Организатор события: <a href="#MEETING_HOST_LINK#">#MEETING_HOST_NAME#</a><br><br> <hr> <br><br> <span style="color: grey">ЭТО СООБЩЕНИЕ СГЕНЕРИРОВАНО СИСТЕМОЙ АВТОМАТИЧЕСКИ! НЕ ОТВЕЧАЙТЕ НА ДАННОЕ СООБЩЕНИЕ!</span>',
                    'BODY_TYPE' => 'html',
                    'BCC' => '',
                    'REPLY_TO' => '',
                    'CC' => '',
                    'IN_REPLY_TO' => '',
                    'PRIORITY' => '',
                    'FIELD1_NAME' => '',
                    'FIELD1_VALUE' => '',
                    'FIELD2_NAME' => '',
                    'FIELD2_VALUE' => '',
                    'SITE_TEMPLATE_ID' => '',
                    'ADDITIONAL_FIELD' => [],
                    'LANGUAGE_ID' => 'ru',
                    'EVENT_TYPE' => '[ COMMITTEES_EVENT_DELETE ] Календарь: отмена события',
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_INTERSECTION',
                    'LID' => ['s1'],
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => 'Календарь: пересечение событий',
                    'MESSAGE' => '#MESSAGE#',
                    'BODY_TYPE' => 'html',
                    'BCC' => '',
                    'REPLY_TO' => '',
                    'CC' => '',
                    'IN_REPLY_TO' => '',
                    'PRIORITY' => '',
                    'FIELD1_NAME' => '',
                    'FIELD1_VALUE' => '',
                    'FIELD2_NAME' => '',
                    'FIELD2_VALUE' => '',
                    'SITE_TEMPLATE_ID' => '',
                    'ADDITIONAL_FIELD' => [],
                    'LANGUAGE_ID' => 'ru',
                    'EVENT_TYPE' => '[ COMMITTEES_EVENT_INTERSECTION ] Возникло пересечение событий',
                ],
                [
                    'EVENT_NAME' => 'COMMITTEES_EVENT_USER_EXCLUDED',
                    'LID' => ['s1'],
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => 'Календарь: исключение из события',
                    'MESSAGE' => 'Уведомление о Вашем исключении из события \'#EVENT_NAME#\' вашего календаря, запланированного на #DATE_FROM# <br><br> <hr> <br><br> <span style="color: grey">ЭТО СООБЩЕНИЕ СГЕНЕРИРОВАНО СИСТЕМОЙ АВТОМАТИЧЕСКИ! НЕ ОТВЕЧАЙТЕ НА ДАННОЕ СООБЩЕНИЕ!</span>',
                    'BODY_TYPE' => 'html',
                    'BCC' => '',
                    'REPLY_TO' => '',
                    'CC' => '',
                    'IN_REPLY_TO' => '',
                    'PRIORITY' => '',
                    'FIELD1_NAME' => '',
                    'FIELD1_VALUE' => '',
                    'FIELD2_NAME' => '',
                    'FIELD2_VALUE' => '',
                    'SITE_TEMPLATE_ID' => '',
                    'ADDITIONAL_FIELD' => [],
                    'LANGUAGE_ID' => 'ru',
                    'EVENT_TYPE' => '[ COMMITTEES_EVENT_USER_EXCLUDED ] Календарь: исключение из события',
                ]
            ];
    }
}
