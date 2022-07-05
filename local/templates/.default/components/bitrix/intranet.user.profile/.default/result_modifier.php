<?php

use Bitrix\Main\Loader;

if (Loader::includeModule('fbit.inspector')) {
    $Filter = new Fbit\Inspector\Filter();
    $Filter->GetDeleteDepatments(); // Подразделения Наблюдателей.
    $InspectorDepartment = $Filter->DeleteDepatments;

    foreach ($arResult['User']['MANAGERS'] as $index => $manager) {
        if (array_intersect($manager['UF_DEPARTMENT'], $InspectorDepartment)) {
            unset($arResult['User']['MANAGERS'][$index]);
        }
    }
    foreach ($arResult['User']['SUBORDINATES'] as $index => $subordinate) {
        if (array_intersect($subordinate['UF_DEPARTMENT'], $InspectorDepartment)) {
            unset($arResult['User']['SUBORDINATES'][$index]);
        }
    }
    foreach ($arResult['User']['UF_DEPARTMENT'] as $index => $depId) {
        if (in_array($depId, $InspectorDepartment)) {
            unset($arResult['User']['UF_DEPARTMENT'][$index]);
        }
    }
}
