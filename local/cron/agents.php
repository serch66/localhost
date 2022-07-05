<?php
/**
 * Скрипт для выполнения
 * перед использованием добавить в файл dbconn.php
 * ```
 * if(!(defined("CHK_EVENT") && CHK_EVENT===true))
 * define("BX_CRONTAB_SUPPORT", true);
 * ```
 * Удалить:
 * ```
 * define("BX_CRONTAB_SUPPORT", true);
 * define("BX_CRONTAB", true);
 * ```
 */

use Bitrix\Sender\MailingManager;

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);
define('CHK_EVENT', true);
define('BX_WITH_ON_AFTER_EPILOG', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0);
@ignore_user_abort(true);

CAgent::CheckAgents();
define("BX_CRONTAB_SUPPORT", true);
define("BX_CRONTAB", true);
CEvent::CheckEvents();

if (CModule::IncludeModule('sender')) {
    MailingManager::checkPeriod(false);
    MailingManager::checkSend();
}

CMain::FinalActions();
