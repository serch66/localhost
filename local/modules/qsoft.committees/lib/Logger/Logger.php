<?php

namespace Qsoft\Committees\Logger;

use Bitrix\Main\Context;
use Monolog\Handler\RotatingFileHandler;
use Qsoft\Committees\Admin\Options;

class Logger
{
    private static $instance;

    /**
     * Возвращает логгер с указанным уменем
     *
     * @param string $name
     * @return \Monolog\Logger
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    public static function get(string $name): \Monolog\Logger
    {
        if (is_null(self::$instance)) {
            $path = Options::getOption('path_to_log');
            self::$instance = new \Monolog\Logger($name);
            self::$instance->pushHandler(
                new RotatingFileHandler(
                    Context::getCurrent()->getServer()->getDocumentRoot() . $path . $name . '.log',
                    7,
                    \Monolog\Logger::INFO
                )
            );
        }

        return self::$instance;
    }
}
