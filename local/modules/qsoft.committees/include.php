<?php

namespace Qsoft\Committees;

use Qsoft\Committees\Tools;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;
/**
 * Имя модуля
 */
const MODULE_ID = 'qsoft.committees';

IncludeModuleLangFile(__FILE__);

spl_autoload_register(
    function ($class) {
        $prefix = 'Qsoft\\Committees\\';
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

$pathJs = Tools::getPathForStatic().'/js/'. MODULE_ID;
$pathCss = Tools::getPathForStatic().'/css/'.MODULE_ID;
$pathLang = (Tools::isLocal() ? '/local' : '/bitrix' ). '/modules/'.MODULE_ID.'/lang/'.LANGUAGE_ID;

\CJSCore::RegisterExt(
    'qsoft_committees_sync',
    [
        'js' => $pathJs.'/sync.js',
        'css' => $pathCss.'/sync.css',
        'lang' => $pathLang.'/js/sync.php',
        'rel' => ['jquery2', 'utils', 'date']
    ]
);
