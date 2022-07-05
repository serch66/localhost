<?php
namespace Qsoft\Committees\Orm;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\BooleanField,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\TextField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class EventTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PARENT_ID int optional
 * <li> ACTIVE bool ('N', 'Y') optional default 'Y'
 * <li> DELETED bool ('N', 'Y') optional default 'N'
 * <li> CAL_TYPE string(100) optional
 * <li> OWNER_ID int mandatory
 * <li> NAME string(255) optional
 * <li> DATE_FROM datetime optional
 * <li> DATE_TO datetime optional
 * <li> ORIGINAL_DATE_FROM datetime optional
 * <li> TZ_FROM string(50) optional
 * <li> TZ_TO string(50) optional
 * <li> TZ_OFFSET_FROM int optional
 * <li> TZ_OFFSET_TO int optional
 * <li> DATE_FROM_TS_UTC int optional
 * <li> DATE_TO_TS_UTC int optional
 * <li> DT_SKIP_TIME string(1) optional
 * <li> DT_LENGTH int optional
 * <li> EVENT_TYPE string(50) optional
 * <li> CREATED_BY int mandatory
 * <li> DATE_CREATE datetime optional
 * <li> TIMESTAMP_X datetime optional
 * <li> DESCRIPTION text optional
 * <li> DT_FROM datetime optional
 * <li> DT_TO datetime optional
 * <li> PRIVATE_EVENT string(10) optional
 * <li> ACCESSIBILITY string(10) optional
 * <li> IMPORTANCE string(10) optional
 * <li> IS_MEETING string(1) optional
 * <li> MEETING_STATUS string(1) optional
 * <li> MEETING_HOST int optional
 * <li> MEETING text optional
 * <li> LOCATION string(255) optional
 * <li> REMIND text optional
 * <li> COLOR string(10) optional
 * <li> TEXT_COLOR string(10) optional
 * <li> RRULE string(255) optional
 * <li> EXDATE text optional
 * <li> DAV_XML_ID string(255) optional
 * <li> G_EVENT_ID string(255) optional
 * <li> DAV_EXCH_LABEL string(255) optional
 * <li> CAL_DAV_LABEL string(255) optional
 * <li> VERSION string(255) optional
 * <li> ATTENDEES_CODES text optional
 * <li> RECURRENCE_ID int optional
 * <li> RELATIONS string(255) optional
 * <li> SEARCHABLE_CONTENT text optional
 * <li> SECTION_ID int optional
 * </ul>
 *
 * @package Qsoft\Committees\Orm
 **/

class EventTable extends DataManager
{

	public static function getDeletedEventsByParent(int $parentId, array $select = []): array
	{
		$deletedEvents = [];

		$arFilter = [
			'PARENT_ID' => $parentId,
			'DELETED' => 'Y'
		];

		$arSelect = [
			'ID',
			'CREATED_BY',
			'UF_WAS_NOTIFIED',
			'NAME',
			'DATE_FROM',
			'DATE_TO'
		];

		$arSelect = array_merge($arSelect, $select);

		$rsDeletedEvents = EventTable::getList([
			'filter' => $arFilter,
			'select' => $arSelect
		]);

		while ($deletedEvent = $rsDeletedEvents->fetch()) {
			$deletedEvents[] = $deletedEvent;
		}

		return $deletedEvents;
	}

	public static function getEventName(int $eventId): string
	{
		$name = '';

		$eventInfo = self::getList([
			'filter' => ['ID' => $eventId],
			'select' => ['NAME'],
			'limit' => 1
		])->fetch();

		if ($eventInfo) {
			$name = $eventInfo['NAME'];
		}

		return $name;
	}

	public static function getEventLink(int $userId, int $eventId, string $dateFrom): string
	{
		return $_SERVER['HTTP_ORIGIN'] .
			'/company/personal/user/' .
			$userId .
			'/calendar/?EVENT_ID=' .
			$eventId .
			'&EVENT_DATE=' .
			$dateFrom
			;
	}

	public static function setMeetingStatusByParent(int $parentId, string $status): void
	{
		global $DB;

		$strSql =
			"UPDATE b_calendar_event SET " .
			$DB->PrepareUpdate("b_calendar_event", ["MEETING_STATUS" => $status]) .
			" WHERE PARENT_ID=" .
			$parentId
		;

		$DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: "  . __LINE__);
	}

	public static function acceptInvitationsByParent(int $parentId, array $userIds): void
	{
		global $CACHE_MANAGER;

		self::setMeetingStatusByParent($parentId, 'Y');
		\CCalendar::UpdateCounter($userIds);

		foreach ($userIds as $userId) {
			$CACHE_MANAGER->ClearByTag('calendar_user_' . $userId);
		}

		\CCalendar::ClearCache(array('attendees_list', 'event_list'));
	}

    public static function acceptInvitation(int $eventId, int $userId): void
    {
        global $CACHE_MANAGER;

        self::setMeetingStatus($eventId, 'Y');
        \CCalendar::UpdateCounter($userId);

        $CACHE_MANAGER->ClearByTag('calendar_user_' . $userId);

        \CCalendar::ClearCache(array('attendees_list', 'event_list'));
    }

    public static function setMeetingStatus(int $eventId, string $status): void
    {
        global $DB;

        $strSql =
            "UPDATE b_calendar_event SET " .
            $DB->PrepareUpdate("b_calendar_event", ["MEETING_STATUS" => $status]) .
            " WHERE ID=" .
            $eventId
        ;

        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: "  . __LINE__);
    }


    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_calendar_event';
    }

    public static function getUfId()
    {
        return 'CALENDAR_EVENT';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),
            (new IntegerField('PARENT_ID',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_PARENT_ID_FIELD')),
            (new BooleanField('ACTIVE',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_ACTIVE_FIELD'))
                ->configureValues('N', 'Y')
                ->configureDefaultValue('Y'),
            (new BooleanField('DELETED',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DELETED_FIELD'))
                ->configureValues('N', 'Y')
                ->configureDefaultValue('N'),
            (new StringField('CAL_TYPE',
                             [
                                 'validation' => [__CLASS__, 'validateCalType']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_CAL_TYPE_FIELD')),
            (new IntegerField('OWNER_ID',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_OWNER_ID_FIELD'))
                ->configureRequired(true),
            (new StringField('NAME',
                             [
                                 'validation' => [__CLASS__, 'validateName']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_NAME_FIELD')),
            (new DatetimeField('DATE_FROM',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DATE_FROM_FIELD')),
            (new DatetimeField('DATE_TO',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DATE_TO_FIELD')),
            (new DatetimeField('ORIGINAL_DATE_FROM',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_ORIGINAL_DATE_FROM_FIELD')),
            (new StringField('TZ_FROM',
                             [
                                 'validation' => [__CLASS__, 'validateTzFrom']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TZ_FROM_FIELD')),
            (new StringField('TZ_TO',
                             [
                                 'validation' => [__CLASS__, 'validateTzTo']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TZ_TO_FIELD')),
            (new IntegerField('TZ_OFFSET_FROM',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TZ_OFFSET_FROM_FIELD')),
            (new IntegerField('TZ_OFFSET_TO',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TZ_OFFSET_TO_FIELD')),
            (new IntegerField('DATE_FROM_TS_UTC',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DATE_FROM_TS_UTC_FIELD')),
            (new IntegerField('DATE_TO_TS_UTC',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DATE_TO_TS_UTC_FIELD')),
            (new StringField('DT_SKIP_TIME',
                             [
                                 'validation' => [__CLASS__, 'validateDtSkipTime']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DT_SKIP_TIME_FIELD')),
            (new IntegerField('DT_LENGTH',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DT_LENGTH_FIELD')),
            (new StringField('EVENT_TYPE',
                             [
                                 'validation' => [__CLASS__, 'validateEventType']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_EVENT_TYPE_FIELD')),
            (new IntegerField('CREATED_BY',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_CREATED_BY_FIELD'))
                ->configureRequired(true),
            (new DatetimeField('DATE_CREATE',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DATE_CREATE_FIELD')),
            (new DatetimeField('TIMESTAMP_X',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TIMESTAMP_X_FIELD')),
            (new TextField('DESCRIPTION',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DESCRIPTION_FIELD')),
            (new DatetimeField('DT_FROM',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DT_FROM_FIELD')),
            (new DatetimeField('DT_TO',
                               []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DT_TO_FIELD')),
            (new StringField('PRIVATE_EVENT',
                             [
                                 'validation' => [__CLASS__, 'validatePrivateEvent']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_PRIVATE_EVENT_FIELD')),
            (new StringField('ACCESSIBILITY',
                             [
                                 'validation' => [__CLASS__, 'validateAccessibility']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_ACCESSIBILITY_FIELD')),
            (new StringField('IMPORTANCE',
                             [
                                 'validation' => [__CLASS__, 'validateImportance']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_IMPORTANCE_FIELD')),
            (new StringField('IS_MEETING',
                             [
                                 'validation' => [__CLASS__, 'validateIsMeeting']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_IS_MEETING_FIELD')),
            (new StringField('MEETING_STATUS',
                             [
                                 'validation' => [__CLASS__, 'validateMeetingStatus']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_MEETING_STATUS_FIELD')),
            (new IntegerField('MEETING_HOST',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_MEETING_HOST_FIELD')),
            (new TextField('MEETING',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_MEETING_FIELD')),
            (new StringField('LOCATION',
                             [
                                 'validation' => [__CLASS__, 'validateLocation']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_LOCATION_FIELD')),
            (new TextField('REMIND',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_REMIND_FIELD')),
            (new StringField('COLOR',
                             [
                                 'validation' => [__CLASS__, 'validateColor']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_COLOR_FIELD')),
            (new StringField('TEXT_COLOR',
                             [
                                 'validation' => [__CLASS__, 'validateTextColor']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_TEXT_COLOR_FIELD')),
            (new StringField('RRULE',
                             [
                                 'validation' => [__CLASS__, 'validateRrule']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_RRULE_FIELD')),
            (new TextField('EXDATE',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_EXDATE_FIELD')),
            (new StringField('DAV_XML_ID',
                             [
                                 'validation' => [__CLASS__, 'validateDavXmlId']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DAV_XML_ID_FIELD')),
            (new StringField('G_EVENT_ID',
                             [
                                 'validation' => [__CLASS__, 'validateGEventId']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_G_EVENT_ID_FIELD')),
            (new StringField('DAV_EXCH_LABEL',
                             [
                                 'validation' => [__CLASS__, 'validateDavExchLabel']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_DAV_EXCH_LABEL_FIELD')),
            (new StringField('CAL_DAV_LABEL',
                             [
                                 'validation' => [__CLASS__, 'validateCalDavLabel']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_CAL_DAV_LABEL_FIELD')),
            (new StringField('VERSION',
                             [
                                 'validation' => [__CLASS__, 'validateVersion']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_VERSION_FIELD')),
            (new TextField('ATTENDEES_CODES',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_ATTENDEES_CODES_FIELD')),
            (new IntegerField('RECURRENCE_ID',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_RECURRENCE_ID_FIELD')),
            (new StringField('RELATIONS',
                             [
                                 'validation' => [__CLASS__, 'validateRelations']
                             ]
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_RELATIONS_FIELD')),
            (new TextField('SEARCHABLE_CONTENT',
                           []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_SEARCHABLE_CONTENT_FIELD')),
            (new IntegerField('SECTION_ID',
                              []
            ))->configureTitle(Loc::getMessage('EVENT_ENTITY_SECTION_ID_FIELD')),
        ];
    }

    /**
     * Returns validators for CAL_TYPE field.
     *
     * @return array
     */
    public static function validateCalType()
    {
        return [
            new LengthValidator(null, 100),
        ];
    }

    /**
     * Returns validators for NAME field.
     *
     * @return array
     */
    public static function validateName()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for TZ_FROM field.
     *
     * @return array
     */
    public static function validateTzFrom()
    {
        return [
            new LengthValidator(null, 50),
        ];
    }

    /**
     * Returns validators for TZ_TO field.
     *
     * @return array
     */
    public static function validateTzTo()
    {
        return [
            new LengthValidator(null, 50),
        ];
    }

    /**
     * Returns validators for DT_SKIP_TIME field.
     *
     * @return array
     */
    public static function validateDtSkipTime()
    {
        return [
            new LengthValidator(null, 1),
        ];
    }

    /**
     * Returns validators for EVENT_TYPE field.
     *
     * @return array
     */
    public static function validateEventType()
    {
        return [
            new LengthValidator(null, 50),
        ];
    }

    /**
     * Returns validators for PRIVATE_EVENT field.
     *
     * @return array
     */
    public static function validatePrivateEvent()
    {
        return [
            new LengthValidator(null, 10),
        ];
    }

    /**
     * Returns validators for ACCESSIBILITY field.
     *
     * @return array
     */
    public static function validateAccessibility()
    {
        return [
            new LengthValidator(null, 10),
        ];
    }

    /**
     * Returns validators for IMPORTANCE field.
     *
     * @return array
     */
    public static function validateImportance()
    {
        return [
            new LengthValidator(null, 10),
        ];
    }

    /**
     * Returns validators for IS_MEETING field.
     *
     * @return array
     */
    public static function validateIsMeeting()
    {
        return [
            new LengthValidator(null, 1),
        ];
    }

    /**
     * Returns validators for MEETING_STATUS field.
     *
     * @return array
     */
    public static function validateMeetingStatus()
    {
        return [
            new LengthValidator(null, 1),
        ];
    }

    /**
     * Returns validators for LOCATION field.
     *
     * @return array
     */
    public static function validateLocation()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for COLOR field.
     *
     * @return array
     */
    public static function validateColor()
    {
        return [
            new LengthValidator(null, 10),
        ];
    }

    /**
     * Returns validators for TEXT_COLOR field.
     *
     * @return array
     */
    public static function validateTextColor()
    {
        return [
            new LengthValidator(null, 10),
        ];
    }

    /**
     * Returns validators for RRULE field.
     *
     * @return array
     */
    public static function validateRrule()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for DAV_XML_ID field.
     *
     * @return array
     */
    public static function validateDavXmlId()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for G_EVENT_ID field.
     *
     * @return array
     */
    public static function validateGEventId()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for DAV_EXCH_LABEL field.
     *
     * @return array
     */
    public static function validateDavExchLabel()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for CAL_DAV_LABEL field.
     *
     * @return array
     */
    public static function validateCalDavLabel()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for VERSION field.
     *
     * @return array
     */
    public static function validateVersion()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }

    /**
     * Returns validators for RELATIONS field.
     *
     * @return array
     */
    public static function validateRelations()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}
