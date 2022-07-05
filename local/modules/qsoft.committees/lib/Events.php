<?
namespace Qsoft\Committees;

use Bitrix\Main\Localization\Loc;
use Qsoft\Committees\Handlers\Calendar;
use Qsoft\Committees\Handlers\Socialnetwork;

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
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->addEventHandler('calendar', 'OnAfterCalendarEntryAdd', [Calendar::class, 'onAfterCalendarEntryAdd']);
        $eventManager->addEventHandler('calendar', 'OnAfterCalendarEntryUpdate', [Calendar::class, 'onAfterCalendarEntryUpdate']);
        $eventManager->addEventHandler('calendar', 'OnAfterCalendarEventDelete', [Calendar::class, 'OnAfterCalendarEventDelete']);
        $eventManager->addEventHandler('socialnetwork', 'OnBeforeSocNetLogAdd', [Socialnetwork::class, 'onBeforeSocNetLogAdd']);
    }
}
