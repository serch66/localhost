<?php

namespace Qsoft\Committees\Handlers;

class Socialnetwork
{
	private static $logDisabled = false;

    public static function disableLog()
    {
        self::$logDisabled = true;
    }

    public static function endableLog()
    {
        self::$logDisabled = false;
    }

    public static function onBeforeSocNetLogAdd($arFields)
	{
		if ($arFields['EVENT_ID'] === 'calendar' && self::$logDisabled) {
		    return false;
        }

		return true;
	}
}
