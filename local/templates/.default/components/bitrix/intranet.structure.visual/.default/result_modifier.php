<?php

use Bitrix\Main\Loader;

if (Loader::includeModule('fbit.inspector')) {
    $Filter = new Fbit\Inspector\Filter();
    $Filter->GetDeleteDepatments(); // Подразделения Наблюдателей.
    $InspectorDepartment = $Filter->DeleteDepatments;

    foreach ($Filter->DeleteDepatments as $depId) {
        /** @var array $arResult */
        unset($arResult['ENTRIES'][$depId]);
    }
}
