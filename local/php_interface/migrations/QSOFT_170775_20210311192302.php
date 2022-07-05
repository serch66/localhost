<?php

namespace Sprint\Migration;


class QSOFT_170775_20210311192302 extends Version
{
    protected $description = "Удаление сообщения \"Я в команде\"";

    protected $moduleVersion = "3.25.1";

    public function up()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnAfterUserAuthorize', 'im', 'Bitrix\Im\Integration\Intranet\User', 'onInviteAccepted');
    }

    public function down()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnAfterUserAuthorize', 'im', 'Bitrix\Im\Integration\Intranet\User', 'onInviteAccepted');
    }
}
