<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class qsoft_personal extends CModule
{
    public $MODULE_ID = "qsoft.personal";

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

        $this->MODULE_PATH = $this->getModulePath();
        include $this->MODULE_PATH.'/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage('QSOFT_PERSONAL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('QSOFT_PERSONAL_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('QSOFT_PERSONAL_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('QSOFT_PERSONAL_MODULE_PARTNER_URL');
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

    public function registerModule()
    {
        RegisterModule($this->MODULE_ID);
    }

    public function unRegisterModule()
    {
        UnRegisterModule($this->MODULE_ID);
    }

    function installFiles()
    {
        try {
            if ($this->isLocal()) {
                CopyDirFiles($this->MODULE_PATH."/install/static/js", $_SERVER[ "DOCUMENT_ROOT" ]."/local/static/js", true, true);
                CopyDirFiles($this->MODULE_PATH."/install/static/css", $_SERVER[ "DOCUMENT_ROOT" ]."/local/static/css", true, true);

            } else {
                CopyDirFiles($this->MODULE_PATH."/install/static/js", $_SERVER[ "DOCUMENT_ROOT" ]."/bitrix/js", true, true);
                CopyDirFiles($this->MODULE_PATH."/install/static/css", $_SERVER[ "DOCUMENT_ROOT" ]."/bitrix/css", true, true);
            }
        } catch (\Exception | \Throwable $e) {

        }

        return true;
    }

    function unInstallFiles()
    {
        try {
            $dir = $this->MODULE_PATH . '/install/admin/' . ($this->isLocal() ? 'local' : 'bitrix');

            if ($this->isLocal()) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/static/js/' . $this->MODULE_ID)) {
                    \Bitrix\Main\IO\Directory::deleteDirectory(
                        $_SERVER["DOCUMENT_ROOT"] . '/local/static/js/' . $this->MODULE_ID
                    );
                }
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/static/css/' . $this->MODULE_ID)) {
                    \Bitrix\Main\IO\Directory::deleteDirectory(
                        $_SERVER["DOCUMENT_ROOT"] . '/local/static/css/' . $this->MODULE_ID
                    );
                }
            } else {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/bitrix/js/' . $this->MODULE_ID)) {
                    \Bitrix\Main\IO\Directory::deleteDirectory(
                        $_SERVER["DOCUMENT_ROOT"] . '/bitrix/js/' . $this->MODULE_ID
                    );
                }
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/bitrix/css/' . $this->MODULE_ID)) {
                    \Bitrix\Main\IO\Directory::deleteDirectory(
                        $_SERVER["DOCUMENT_ROOT"] . '/bitrix/css/' . $this->MODULE_ID
                    );
                }
            }
        } catch (\Exception | \Throwable $e) {

        }

        return true;
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice(
            $modulePath,
            0,
            array_search($this->MODULE_ID, $modulePath) + 1
        );

        return join('/', $modulePath);
    }

    protected function isLocal()
    {
        if (strpos($this->MODULE_PATH, 'local/modules') !== false) {
            return true;
        }

        return false;
    }
}
