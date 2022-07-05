<?php

use Bitrix\Main\Localization\Loc;
use Qsoft\Main\Events;

Loc::loadMessages(__FILE__);

class qsoft_main extends CModule
{
    public $MODULE_ID = "qsoft.main";

    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("QSOFT_MAIN_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("QSOFT_MAIN_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("QSOFT_MAIN_MODULE_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("QSOFT_MAIN_MODULE_PARTNER_URL");
    }

    public function DoInstall()
    {
        $this->installFiles();
        $this->registerModule();
    }

    public function DoUninstall()
    {
        $this->unInstallFiles();
        $this->unRegisterModule();
    }

    public function registerModule(): void
    {
        RegisterModule($this->MODULE_ID);

        RegisterModuleDependences(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Events::class,
            'registerHandlers'
        );
    }

    public function unRegisterModule(): void
    {
        UnRegisterModule($this->MODULE_ID);

        UnRegisterModuleDependences(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Events::class,
            'registerHandlers'
        );
    }
}
