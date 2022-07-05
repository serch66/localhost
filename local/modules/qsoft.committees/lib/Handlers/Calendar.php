<?php

namespace Qsoft\Committees\Handlers;

use Bitrix\Main\Type\DateTime;
use Qsoft\Committees\Admin\Options;
use Qsoft\Committees\Orm\EventTable;
use Qsoft\Committees\Entity\User;
use Qsoft\Committees\Service\Notifications;

class Calendar
{
	public static function onAfterCalendarEntryAdd($eventId, $fields)
	{
		if (!isset($fields['UF_COMMITTEE_EVENT_ID']) || is_null($fields['UF_COMMITTEE_EVENT_ID'])) {
		    Notifications::sendBitrixEventNotification('COMMITTEES_EVENT_ADD', $fields);
		} else {
            EventTable::acceptInvitation($eventId, $fields['OWNER_ID']);
		}

        //запоминаем дату и время начала и окончания события в пользователские поля, для отслеживания изменений,
        //так как события onBeforeCalendarEntryUpdate в битриксе нет
        \CCalendarEvent::UpdateUserFields(
            $eventId,
            [
                'UF_PREV_DATE_FROM' => new DateTime($fields['DATE_FROM']),
                'UF_PREV_DATE_TO' => new DateTime($fields['DATE_TO'])
            ]
        );
	}

	public static function onAfterCalendarEntryUpdate($eventId, $fields)
	{
		if (!isset($fields['UF_COMMITTEE_EVENT_ID']) || is_null($fields['UF_COMMITTEE_EVENT_ID'])) {

			if ((int)$fields['ID'] === (int)$fields['PARENT_ID'] && !isset($fields['G_EVENT_ID'])) {
				self::notifyExcludedUsers($fields);
			}

			Notifications::sendBitrixEventNotification('COMMITTEES_EVENT_UPDATE', $fields);
		} else {
		    EventTable::acceptInvitation($eventId, $fields['OWNER_ID']);
        }

        //если у события изменились даты, то отправляем его на повторную проверку пересечений
        if (self::areDatesChanging($eventId, $fields)) {
            \CCalendarEvent::UpdateUserFields($eventId, ['UF_INTERSECTIONS_CHECKED' => 0]);
        }
	}

	public static function OnAfterCalendarEventDelete($eventId, $fields)
	{

		if ((!isset($fields['UF_COMMITTEE_EVENT_ID']) || is_null($fields['UF_COMMITTEE_EVENT_ID'])) && (int)$fields['ID'] === (int)$fields['PARENT_ID']) {

			$attendeesIds = [];
			$notifOnlyActive = Options::getNotifOnlyActiveOption() === 'Y';

			if (isset($fields['G_EVENT_ID'])) {
				global $USER;

				$userId = $USER->GetID();

				$attendeesIds[] = $userId;
				$hostId = $userId;
				$hostName = $USER->GetFullName();
				$eventName = EventTable::getEventName($fields['ID']);

			} else {

				foreach ($fields['ATTENDEE_LIST'] as $attendeeInfo) {
                    $group = [];
					if ($notifOnlyActive) {
					    if (User::isActive($attendeeInfo['id'], $group)) {
							$attendeesIds[] = $attendeeInfo['id'];
						}
					} else {
						$attendeesIds[] = $attendeeInfo['id'];
					}
				}

				$hostId = $fields['MEETING_HOST'];
				$hostName = $fields['~MEETING']['HOST_NAME'];
				$eventName = $fields['NAME'];
			}

			if (!empty($attendeesIds)) {
                $mailFields = [
                    "EMAIL_TO" => implode(',', User::getEmailByIds($attendeesIds)),
                    "EVENT_NAME" => $eventName,
                    "DATE_FROM" => $fields['DATE_FROM'],
                    "MEETING_HOST_LINK" => User::getPathToPersonalCard($hostId),
                    "MEETING_HOST_NAME" => $hostName
                ];

                Notifications::sendNotification('COMMITTEES_EVENT_DELETE', $mailFields);
            }
		}
	}

	private static function areDatesChanging(int $eventId, $fields) : bool
	{
		if (
			!isset($fields['DATE_FROM']) &&
			!isset($fields['DATE_TO'])
		) {
			return false;
		}

		$event = EventTable::getList(
			[
				'filter' => ['ID' => $eventId],
				'select' => ['ID', 'DATE_FROM', 'DATE_TO', 'UF_PREV_DATE_FROM', 'UF_PREV_DATE_TO']
			]
		)->fetch();

		$ufFields = [];

		if (empty($event['UF_PREV_DATE_FROM'])) {

			$ufFields['UF_PREV_DATE_FROM'] = $event['DATE_FROM'];

		} elseif ($event['DATE_FROM']->getTimestamp() !== $event['UF_PREV_DATE_FROM']->getTimestamp()) {

			$ufFields['UF_PREV_DATE_FROM'] = $event['DATE_FROM'];

		}

		if (empty($event['UF_PREV_DATE_TO'])) {

		    $ufFields['UF_PREV_DATE_TO'] = $event['DATE_TO'];

		} else if ($event['DATE_TO']->getTimestamp() !== $event['UF_PREV_DATE_TO']->getTimestamp()) {

		    $ufFields['UF_PREV_DATE_TO'] = $event['DATE_TO'];

		}

		if (!empty($ufFields)) {
			\CCalendarEvent::UpdateUserFields($eventId, $ufFields);
			return true;
		}

		return false;
	}

	private static function notifyExcludedUsers(array $fields): void
	{
		$deletedEvents = EventTable::getDeletedEventsByParent($fields['PARENT_ID']);
		$notifOnlyActive = Options::getNotifOnlyActiveOption() === 'Y';

		if (!empty($deletedEvents)) {

			$ids = [];

			foreach ($deletedEvents as $deletedEvent) {

				if ($deletedEvent['UF_WAS_NOTIFIED'] !== '1') {
					if ($notifOnlyActive) {
					    $group = [];
					    if (User::isActive($deletedEvent['CREATED_BY'], $group)) {
							$ids[] = $deletedEvent['CREATED_BY'];
						}
					} else {
						$ids[] = $deletedEvent['CREATED_BY'];
					}

					\CCalendarEvent::UpdateUserFields(
						$deletedEvent['ID'],
						[
							'UF_WAS_NOTIFIED' => 1
						]
					);
				}
			}

			if (!empty($ids)) {
				$mailFields = [
					"EMAIL_TO" => implode(',', User::getEmailByIds($ids)),
					"EVENT_NAME" => $fields['NAME'],
					"DATE_FROM" => $fields['DATE_FROM']
				];

				Notifications::sendNotification('COMMITTEES_EVENT_USER_EXCLUDED', $mailFields);
			}
		}
	}
}
