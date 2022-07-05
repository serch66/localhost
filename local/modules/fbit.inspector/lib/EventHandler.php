<?php

namespace Fbit\Inspector;

use Bitrix\Main\Config\Option;

class EventHandler
{
    function OnBeforeUserUpdate(&$arParams)
    {
        global $USER;

        if (!isset($arParams['UF_DEPARTMENT'])) {
            return true;
        }

        if ($USER->IsAdmin()) {
            return true;
        }

        $oldDepartment = \Bitrix\Main\UserTable::GetList([
            'filter' => ['=ID' => $arParams['ID']],
            'select' => ['UF_DEPARTMENT'],
        ])->Fetch()['UF_DEPARTMENT'];

        $Filter = new Filter();
        $Filter->GetDeleteDepatments(); // Подразделения Наблюдателей.
        $InspectorDepartment = $Filter->DeleteDepatments;

        $DeleteDeparmnet = array_diff($oldDepartment, $arParams['UF_DEPARTMENT']);
        $array_intersect = array_intersect($InspectorDepartment, $DeleteDeparmnet);

        if (count($array_intersect) > 0) {
            $arParams['UF_DEPARTMENT'] = $result = array_merge($arParams['UF_DEPARTMENT'], $array_intersect);
        }

        $isInspector = (bool)array_intersect($InspectorDepartment, $arParams['UF_DEPARTMENT']);

        $arParams['UF_EXCLUDE_FROM_SELECTOR'] = $isInspector;

        return true;
    }

    function OnSocNetLogDestinationSearchUsers ($arSearchValue, &$filter, &$select)
    {
        $filter['UF_EXCLUDE_FROM_SELECTOR'] = false;
    }
}
