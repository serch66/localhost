<?php

/**
 * cron command
 * ```
 * * *\/1 * * * [PHP_PATH] -f [SCRIPT_PATH]
 * ```
 * Необходимо устанавливать не большой временной промежуток для выполнения скрипта
 * Рекомендованный промежуток: 24 часа
 */

use Bitrix\Main\Loader;

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);
define('BX_WITH_ON_AFTER_EPILOG', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0);
@ignore_user_abort(true);

if (!Loader::includeModule('ldap')) {
    echo 'module ldap is not installed';
    exit;
}

$logger = \Qsoft\Main\Logger\Logger::channel('ldap');
$logger->info('Старт импорта из AD');

$cnt = 0;

try {
    $cnt = (int)\CLdapServer::Sync(1);
} catch (\Exception | \Throwable $e) {
    $logger->critical($e->getMessage(), [$e->getTraceAsString()]);
}

$logger->info('Импорт завершен. Изменено\добавлено ' . $cnt . ' пользователей');

dump('OK');
