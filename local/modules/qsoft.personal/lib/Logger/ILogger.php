<?php

namespace Qsoft\Personal\Logger;

/**
 * Интерфейс описывает методы для логирования
 */
interface ILogger
{
    /**
     * Метод предназначен для занесения в лог сообщений с пометкой средней важности
     * @param string $message
     * @return mixed
     */
    public function warning(string $message);

    /**
     * Метод предназначен для занесения в лог информационных сообщений
     * @param string $message
     * @return mixed
     */
    public function info(string $message);

    /**
     * Метод предназначен для занесения в лог сообщений о произошедших ошибках
     * @param string $message
     * @return mixed
     */
    public function error(string $message);
}
