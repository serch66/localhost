<?php
namespace Custom\Handler;

use Bitrix\Main\Page\Asset;
use Custom\Helper\Url;
use CJSCore;
use Custom\Helper\CrmActivityPermissions;

class Ui
{
    public static function includeCss($page = '')
    {
        if(strpos($page, 'crm') !== false && strpos($page, 'details')) {
            self::cssCrmDetails($page);
        }
    }
    
    public static function includeJs($page = '')
    {
        CJSCore::Init(['jquery']);
        if(strpos($page, 'crm') !== false && strpos($page, 'details')) {
            self::jsCrmDetails($page);
        }        
    }
    
    public static function cssCrmDetails($page)
    {
        return true;
    }
    
    public static function jsCrmDetails($page)
    {
        if(CrmActivityPermissions::initHideEmailCallActivities()) {
            /* 
             * Подключаем JS для скрытия дел в карточке
             * по правилам для сотрудников МЖД 
             */
            Asset::getInstance()->addJs('/local/js/ui_custom/HideCrmActivities.js');
            Asset::getInstance()->addString(
                '<script>BX.ready(function () { BX.CheckActivityReadPermission.Init(); });</script>'
            );
            /* ************************************ */
        }
    }
}
