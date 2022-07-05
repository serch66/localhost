#!/usr/bin/php
<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . "/../../..");

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", 'Y');
define("NO_AGENT_STATISTIC",'Y');
define("NO_AGENT_CHECK", true);
define("DisableEventsCheck", true);
define('NEED_AUTH', false);
define('NOT_CHECK_PERMISSIONS', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Qsoft\Committees\Logger\Logger;
use Qsoft\Committees\Exception\KafkaReloadException;

Bitrix\Main\Loader::includeModule('qsoft.committees');

$error = "";
$counter = 0;
$logger = Logger::get('kafka_upload');

try {
    a:	$logger->info('Слушатель запущен');
    b:  $eventsInfo = new Qsoft\Committees\Kafka\Connection($logger);
} catch(KafkaReloadException $e) {
    unset($eventsInfo);
	goto b;
} catch(Throwable $e) {
    if ($e->getFile() == $error){
        $counter += 1;
        if ($counter == 1000){
            $logger->critical("Количество ошибок (".$e->getCode()."): " . $counter);
            $counter = 0;
        }
        unset($eventsInfo);
        goto b;
    }
    $counter = 0;
	$logger->critical("Ошибка: " . $e);
	$logger->critical("Код ошибки: " . $e->getCode());
	$logger->critical("Номер строки, где произошла ошибка: " . $e->getLine());
	$logger->critical("Файл, в котором произошла ошибка: " . $e->getFile());
	$logger->critical("Путь ошибки: " . json_encode($e->getTrace()));
    $logger->critical("Сообщение: " . $e->getMessage());
    $error = $e->getFile();
    unset($eventsInfo);
    goto a;
}