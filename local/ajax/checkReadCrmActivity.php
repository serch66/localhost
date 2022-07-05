<?php
/*
 * Проверяем право на просмотр дел пользователя $userId в карточках CRM для текущего пользователя.
 * Результат  сохраняем в cookie на 3 дня
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $USER, $APPLICATION;
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$userId  = trim($request->get('userId'));
$key	 = md5($USER->GetId() . '_CheckReadCrmActivity_' . $userId);
if(isset($_COOKIE[$key])) {
	die(json_encode(['result' => $_COOKIE[$key]]));
}
$result = Custom\Helper\CrmActivityPermissions::checkReadActivityByUserId($userId);
setcookie($key, $result, time() + 60*60*24*3, '/');
die(json_encode(['result' => $result]));
