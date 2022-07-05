<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/local/modules/fbit.forms/include.php";

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class fbit_forms extends CModule
{
    public $MODULE_ID = "fbit.forms";

    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_PATH;

    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("FBIT_MFORMS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("FBIT_MFORMS_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("FBIT_MFORMS_MODULE_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("FBIT_MFORMS_MODULE_PARTNER_URL");
    }


    public function DoInstall()
    {
        $this->registerModule();
        $this->registerEvents();
    }

    public function registerModule(): void
    {
        RegisterModule($this->MODULE_ID);
    }

    public function registerEvents(): void
    {
        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\Fbit\Forms\EventHandler',
            'onProlog'
        );
    }

    public function DoUninstall()
    {
        $this->unregisterEvents();
        $this->unRegisterModule();
    }

    public function unregisterEvents(): void
    {
    }

    public function unRegisterModule()
    {
        UnRegisterModule($this->MODULE_ID);
    }
}
