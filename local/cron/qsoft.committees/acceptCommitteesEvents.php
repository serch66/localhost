<?php

use Qsoft\Committees\Orm\EventTable;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . "/../../..");

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", 'Y');
define("NO_AGENT_STATISTIC", 'Y');
define("NO_AGENT_CHECK", true);
define("DisableEventsCheck", true);
define('NEED_AUTH', false);
define('NOT_CHECK_PERMISSIONS', true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$parentsInfo = EventTable::getList([
    'filter' => ['!UF_COMMITTEE_EVENT_ID' => null],
    'select' => ['ID']
]);

$parentsIds = [];

while ($parentInfo = $parentsInfo->fetch()) {
    $parentsIds[] = $parentInfo['ID'];
}

$eventsInfo = EventTable::getList([
    'filter' => ['PARENT_ID' => $parentsIds, 'MEETING_STATUS' => 'Q'],
    'select' => ['ID', 'OWNER_ID'],
    'order' => ['ID' => 'ASC'],
    'limit' => 1000
]);

$userIds = [];

while ($eventInfo = $eventsInfo->fetch()) {
    EventTable::acceptInvitation($eventInfo['ID'], $eventInfo['OWNER_ID']);
}
