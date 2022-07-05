<?
namespace Qsoft\Main;

use Bitrix\Main\Localization\Loc;
use Qsoft\Main\Handlers\Ldap;
use Qsoft\Main\Handlers\Main;

Loc::loadMessages(__FILE__);

class Events
{
    /**
     * Добавляет обработчики событий
     *
     * @return void
     */
    public static function registerHandlers()
    {
        //if(defined('QSOFT_EVENT_HANDLERS_DISABLED'))
        //    return;
        
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->addEventHandler('ldap', 'OnLdapBeforeSync', [Ldap::class, 'onLdapBeforeSync']);
        $eventManager->addEventHandler('ldap', 'OnLdapUserFields', [Ldap::class, 'onLdapUserFields']);

        $eventManager->addEventHandler('main', 'OnBeforeProlog', [Main::class, 'onBeforeProlog']);
        $eventManager->addEventHandler('main', 'OnBeforeUserLogin', [Main::class, 'onBeforeUserLogin']);
        $eventManager->addEventHandler('main', 'OnAfterUserAdd', [Main::class, 'onAfterUserAdd']);
        $eventManager->addEventHandler('main', 'OnAfterUserUpdate', [Main::class, 'onAfterUserUpdate']);
        $eventManager->addEventHandler('main', 'OnAfterSetUserGroup', [Main::class, 'onAfterSetUserGroup']);
        $eventManager->addEventHandler('main', 'OnBeforeUserAdd', [Main::class, 'OnBeforeUserAddHandler']);
        
       
    }
}

