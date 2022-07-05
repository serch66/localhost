<?php

namespace Qsoft\Main;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;
/**
 * Имя модуля
 */
const MODULE_ID = 'qsoft.main';

IncludeModuleLangFile(__FILE__);

spl_autoload_register(
    function ($class) {
        $prefix = 'Qsoft\\Main\\';
        $base_dir = __DIR__ . '/lib/';
        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);

        $parts = explode("\\", $relative_class);

        /* fix bitrix module controller loader */
        foreach ($parts as $key => $part) {
            $parts[$key] = ucfirst($part);
        }

        $relative_class = join("\\", $parts);

        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
);

