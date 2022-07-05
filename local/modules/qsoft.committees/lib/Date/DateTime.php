<?php


namespace Qsoft\Committees\Date;

use Bitrix\Main\Type\DateTime as bDateTime;

class DateTime
{
    private const TIME_FORMAT = 'd.m.Y H:i:s';

    /**
     * Возвращает таймстамп по строке
     *
     * @param string $timeString
     * @return int
     */
    public static function getTimestampFromString(string $timeString): int
    {
        if ($timeString === '') {
            return 0;
        }

        return bDateTime::createFromUserTime($timeString)->getTimestamp();
    }

    /**
     * Возвращает стандартный формат времени
     *
     * @return string
     */
    public static function getFormat(): string
    {
        return self::TIME_FORMAT;
    }

    /**
     * Возвращает название часового пояса
     *
     * @return string
     */
    public static function getTimezoneName(): string
    {
        return (new bDateTime)->getTimeZone()->getName();
    }

    /**
     * Возвращает строку времени из таймстампа по стандартному формату
     *
     * @param int $timestamp
     * @return string
     */
    public static function getStringFromTimestamp(int $timestamp): string
    {
        return bDateTime::createFromTimestamp($timestamp)->format(self::TIME_FORMAT);
    }

    /**
     * Проверяет соответствие строки времени указанному формату
     *
     * @param string $time
     * @param string $format
     * @return bool
     */
    public static function validate(string $time, string $format = self::TIME_FORMAT): bool
    {
        return bDateTime::isCorrect($time, $format);
    }
}
