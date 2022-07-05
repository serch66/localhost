<?php

/**
 * cron command
 * ```
 * * *\/1 * * * [PHP_PATH] -f [SCRIPT_PATH]
 * ```
 * Необходимо устанавливать не большой временной промежуток для выполнения скрипта
 * Рекомендованный промежуток: 1 час
 *
 * Скрипт запускает синхронизацию по необходимости согласно настройкам, в том числе учитываю команду ручного запуска
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Qsoft\Personal\Admin\Options;
use Qsoft\Personal\Sync\Manager;
use Qsoft\Personal\Logger\Logger;

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);
define('BX_WITH_ON_AFTER_EPILOG', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0);
@ignore_user_abort(true);

global $USER;

if (!$USER->IsAdmin()) {
    echo 'permission denied';
    exit;
}

if (!Loader::includeModule('iblock')) {
    echo 'module iblock is not installed';
    exit;
}

if (!Loader::includeModule('qsoft.personal')) {
    echo 'module qsoft.personal is not installed';
    exit;
}


$rs = \CIBlockSection::GetList(
    [],
    [
        'IBLOCK_ID' => \Qsoft\Personal\Repository\DirectionsRepository::getIblockId(),
        '>SECTION_ID' => 0
    ],
    false,
    ['ID']
);
while ($ar = $rs->Fetch()) {
    \CIBlockSection::Delete($ar['ID']);
}
echo 'OK';
