<?php

namespace Qsoft\Main\Logger;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

use const Qsoft\Main\MODULE_ID;

Loc::loadMessages(__FILE__);

/**
 * Класс предназначен для обеспечения возможности логирования в классе синхронизации
 * Реализует паттерн Одиночка
 * Class Logger
 */
class Logger implements ILogger
{
    /**
     * @var array Хранит набор инстанций / необходимо для сохранения изоляции между дочерними реализациями
     */
    protected static $instances = [];

    /**
     * @var \Monolog\Logger
     */
    private $logger;
    private $channel;

    public static function channel(?string $name = null, ?int $level = null): self
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new static($name, $level);
        }

        return self::$instances[$name];
    }

    /**
     * Logger constructor.
     * @throws \Exception
     */
    protected function __construct(?string $channel = null, ?int $level = null)
    {
        $this->channel = $channel;
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%][%level_name%] %message% %context% %extra%\n";
        $maxFiles = (int)Option::get(MODULE_ID, 'log_days', 90);
        $level = $level ?? (int)Option::get(MODULE_ID, 'log_level', \Monolog\Logger::INFO);

        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new RotatingFileHandler($this->getFilePath(), $maxFiles, $level);
        $stream->setFormatter($formatter);

        $logger = new \Monolog\Logger($this->channel ?? 'log');
        $logger->pushHandler($stream);

        $this->logger = $logger;
    }

    protected static function enabled(): bool
    {
        return Option::get(MODULE_ID, 'log', 'N') == 'Y';
    }

    /**
     * Объекты созданные по паттерну одиночка нельзя клонировать
     */
    protected function __clone()
    {
    }

    /**
     * Объекты созданные по паттерну одиночка не могут быть сериализованы и заново инициализированы
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception(Loc::getMessage("QSOFT_MAIN_LOGGER_SINGLETON_NOT_WAKEABLE"));
    }

    /**
     * Метод записывает сообщение с пометкой о средней важности в лог
     * @param string $message
     * @param array $context
     * @return mixed|void
     */
    public function warning(string $message, array $context = [], bool $force = false)
    {
        if (!static::enabled() && !$force) {
            return;
        }

        try {
            $this->logger->warning($message, $context);
        } catch (\Exception | \Throwable $e) {
        }
    }

    /**
     * Метод записывает сообщение об ошибке в лог
     * @param string $message
     * @param array $context
     * @return mixed|void
     */
    public function error(string $message, array $context = [], bool $force = false)
    {
        if (!static::enabled() && !$force) {
            return;
        }

        try {
            $this->logger->error($message, $context);
        } catch (\Exception | \Throwable $e) {
        }
    }

    /**
     * Метод записывает сообщение с пометкой о критической ошибке в лог
     * @param string $message
     * @param array $context
     * @return mixed|void
     */
    public function critical(string $message, array $context = [], bool $force = false)
    {
        if (!static::enabled()) {
            return;
        }

        try {
            $this->logger->critical($message, $context);
        } catch (\Exception | \Throwable $e) {
        }
    }

    /**
     * Метод записывает информационное сообщение в лог
     * @param string $message
     * @param array $context
     * @return mixed|void
     */
    public function info(string $message, array $context = [], bool $force = false)
    {
        if (!static::enabled() && !$force) {
            return;
        }

        try {
            $this->logger->info($message, $context);
        } catch (\Exception | \Throwable $e) {
        }
    }

    /**
     * Метод получает путь к файлу для логирования
     * @return string
     * @throws \Exception
     */
    public function getFilePath(): string
    {
        return static::getPath() . '/' .
            ($this->channel ? $this->channel . '/' : '') .
            $this->getFileName();
    }

    /**
     * Метод возвращает название файла для лога
     * @return string
     * @throws \Exception
     */
    public function getFileName(): string
    {
        $now = new \DateTime();
        $nowDate = $now->format('Ymd');

        return 'log_' . ($this->name ?? '') . $nowDate . '.log';
    }

    /**
     * Метод возвращает путь для хранения файлов с логами
     * @return string
     */
    public static function getPath(): string
    {
        $documentRoot = Application::getDocumentRoot();

        return $documentRoot . '/local/logs/' . MODULE_ID;
    }
}
