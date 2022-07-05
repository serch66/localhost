<?php
namespace Custom\Handler;

class EventHandler
{
    /* Регистрация обработчиков событий */
    public static function addEventHandlers()
    {
        AddEventHandler('main', 'OnProlog', [__CLASS__, 'onProlog']);
    }
    
    public static function onProlog()
    {
        global $APPLICATION;
        $page = $APPLICATION->GetCurPage();
        
        /* Подключаем кастомные JS и CSS файлы */
        Ui::includeCss($page);
        Ui::includeJs($page);
        /***************************************/
    }
}
