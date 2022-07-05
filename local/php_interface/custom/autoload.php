<?php
namespace Custom;

/* Автозагрузка классов */
spl_autoload_register(function ($class) {
    if(($pos = strpos($class, __NAMESPACE__ . '\\')) !== false) {
        $arClass   = explode('\\', trim($class, '\\'));
        array_shift($arClass);
        $className = array_pop($arClass);
        $dir       = '';
        if($arClass) {
            $dir      = implode(DIRECTORY_SEPARATOR, $arClass) . DIRECTORY_SEPARATOR;
            $filePath = __DIR__ . '/' .'classes/' . $dir . $className . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            }
        }
    }
});

Handler\EventHandler::addEventHandlers();
