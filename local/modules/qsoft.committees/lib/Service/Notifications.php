<?php


namespace Qsoft\Committees\Service;


use Bitrix\Main\Mail\Event;
use Qsoft\Committees\Admin\Options;
use Qsoft\Committees\Entity\User;
use Qsoft\Committees\Logger\Logger;
use Qsoft\Committees\Orm\EventTable;

class Notifications
{
	public static function sendNotification(string $eventName, array $fields): void
	{
		Event::send(
			[
				"EVENT_NAME" => $eventName,
				"LID" => "s1",
				"C_FIELDS" => $fields
			]
		);
	}

	public static function sendBitrixEventNotification(string $event ,array $fields): void
	{
	    $logger = Logger::get('notifications');
	    try {
            if (isset($fields['G_EVENT_ID'])) {
                global $USER;

                $hostId = (int)$fields['CREATED_BY'];
                $hostName = $USER->GetFullName();
            } elseif (isset($fields['MEETING_HOST'])) {
                $hostId = (int)$fields['MEETING_HOST'];
                $hostName = $fields['~MEETING']['HOST_NAME'];
            } elseif (isset($fields['CREATED_BY'])) {
                $hostId = (int)$fields['CREATED_BY'];
                if ($hostId) {
                    $hostName = User::getFullName($hostId);
                }
            }

            $notifOnlyActive = Options::getNotifOnlyActiveOption() === 'Y';
            $mailFields = [
                "EMAIL_TO"          => implode(',', User::getEmailByIds([$fields['CREATED_BY']])),
                "EVENT_LINK"        => EventTable::getEventLink(
                    $fields['CREATED_BY'],
                    $fields['PARENT_ID'],
                    $fields['DATE_FROM']
                ),
                "EVENT_NAME"        => $fields['NAME'],
                "DATE_FROM"         => $fields['DATE_FROM'],
                "MEETING_HOST_LINK" => $hostId ? User::getPathToPersonalCard($hostId) : '',
                "MEETING_HOST_NAME" => $hostName ?? ''
            ];

            if ($notifOnlyActive) {
                if (User::isActive($fields['OWNER_ID'])) {
                    self::sendNotification($event, $mailFields);
                }
            } else {
                self::sendNotification($event, $mailFields);
            }
        } catch (\Exception $e) {
            $logger->error($e->getMessage(), [$e->getTraceAsString()]);
        } catch (\Throwable $e) {
            $logger->critical($e->getMessage(), [$e->getTraceAsString()]);
        }
	}
}
