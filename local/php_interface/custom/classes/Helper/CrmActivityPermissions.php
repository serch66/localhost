<?php
namespace Custom\Helper;

use Bitrix\Main\UserTable;
use Custom\Helper\Constant;

class CrmActivityPermissions
{
    /* 
     * Инициируем JS по скрытию 
     * email-сообщений и звонков в детальной карточке CRM
     * для сотрудников МЖД
     */
    public static function initHideEmailCallActivities()
    {
		global $USER, $APPLICATION;
        if($USER->IsAdmin()) {
            return false;
        }
        $cookie  = $APPLICATION->get_cookie('InitHideEmailCallActivities');
        if(!empty($cookie)) {
            return $cookie;
        }
        $init    = false;
        $mzdDeps = \CIntranetUtils::getIBlockSectionChildren(Constant::MZD_DEPARTMENT_ID);
        $uDeps   = \CIntranetUtils::GetUserDepartments($USER->GetId());
        if(count(array_intersect($uDeps, $mzdDeps)) > 0 || in_array(3154, $uDeps)) {
            $init = true;
        }
        $APPLICATION->set_cookie("InitHideEmailCallActivities", $init, time()+60*60*24*30);
        
        return $init;
    }
    
    /*
     * Проверяем право на просмотр дел в карточках CRM
     * по ID ответственного за дело для текущего пользователя (МЖД).
     * 
     * Право на просмотр имеют:
     * 1) Сотрудники отделов НЕ МЖД
     * 2) Ответственный за дело
     * 3) Администраторы
     * 4) Начальник 1 и 2 уровня ответственного за дело
     */
    public static function checkReadActivityByUserId($userId)
    {
        global $USER;
        if(!self::initHideEmailCallActivities() || $USER->GetId() == $userId) {
            return 'Y';
        }
        $managers = User::getUserManagers($userId);
        if($USER->GetId() == $managers[0] || $USER->GetId() == $managers[1] || 
		   $USER->GetId() == end($managers) || $USER->GetId() == 1011 || $USER->IsAdmin()) {
            return 'Y';
        }
        return 'N';
    }
}
