<?php

/**
 * cron command
 * ```
 * * *\/1 * * * [PHP_PATH] -f [SCRIPT_PATH]
 * ```
 * Рекомендованный промежуток: 1 час
 *
 * Скрипт запускает синхронизацию по необходимости согласно настройкам
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

if (!Loader::includeModule('qsoft.personal')) {
    echo 'module qsoft.personal is not installed';
    exit;
}

if (!Options::isActive()) {
    echo 'module qsoft.personal is not active';
}

if (Manager::isRun() || Manager::isRun(null, true)) {
    echo 'the process is already running';
}

$fromDate = null;
$run = false;

if (Options::canSyncFull()) {
    $run = true;
} elseif (Options::canSyncChanged()) {
    $run = true;
    $fromDate = Options::getLastSyncDate();
}

if ($run) {
    $sync = new Manager(false,  Logger::channel('sync'));
    $sync->setInterval(15);

    if ($fromDate instanceof DateTime) {
        $sync->setDateFrom($fromDate);
    }

    $sync->start();

    echo 'OK';
} else {
    echo'no synchronization required';
}
