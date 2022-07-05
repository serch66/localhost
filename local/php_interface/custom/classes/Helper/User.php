<?php
namespace Custom\Helper;

class User
{
    /*
     * Получение руководителей сотрудника из вышестоящих подразделений.
     * Сортировка от непосредственного начальника
     */
    public static function getUserManagers($userId = 0)
    {
        if(empty($userId)) {
            global $USER;
            $userId = $USER->GetId();
        }
        $managers   = [];
        $parentDeps = [];
        $userDep    = \CIntranetUtils::GetUserDepartments($userId)[0];
        
        \CModule::IncludeModule('iblock');
        $nav = \CIBlockSection::GetNavChain(false, $userDep, ['ID'], true);
        foreach($nav as $section) {
            $manager = \CIntranetUtils::GetDepartmentManagerID($section['ID']);
            if(!empty($manager)) {
                $managers[] = $manager;
            }
        }
        
        return array_reverse($managers);
    }
}
