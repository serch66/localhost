<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/local/modules/qsoft.committees/include.php";

use Bitrix\Main\Localization\Loc;
use Qsoft\Committees\Events;
use Qsoft\Committees\Install\EventTypeInstaller;
use Qsoft\Committees\Install\EventMessageInstaller;
use Qsoft\Committees\Install\FieldsInstaller;
use Qsoft\Committees\Meeting\Host;

Loc::loadMessages(__FILE__);

class qsoft_committees extends CModule
{
    public $MODULE_ID = "qsoft.committees";

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
        $this->MODULE_PATH = $this->getModulePath();

        $this->MODULE_NAME = Loc::getMessage("QSOFT_COMMITTEES_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("QSOFT_COMMITTEES_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("QSOFT_COMMITTEES_MODULE_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("QSOFT_COMMITTEES_MODULE_PARTNER_URL");
    }

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

    public function DoInstall()
    {
        if (
            !$this->checkModule('calendar') ||
            !$this->checkRequiredPackages() ||
            !Host::createHostUser() ||
            !$this->createUserFields() ||
            !$this->createEventTypes() ||
            !$this->createEventTemplateMessages()
        ) {
            return false;
        }

        $this->installFiles();
        $this->registerModule();
    }

    public function DoUninstall()
    {
        if (
            !$this->checkModule('calendar') ||
//            !Host::deleteHostUser() ||
            !$this->unInstallFiles() ||
//            !$this->deleteUserFields() ||
            !$this->deleteEventTemplateMessages() ||
            !$this->deleteEventTypes()
        ) {
            return false;
        }

        $this->unRegisterModule();

        return true;
    }

    private function createEventTypes(): bool
    {
        foreach (EventTypeInstaller::getRequiredEventTypes() as $eventType) {
            $result = EventTypeInstaller::install($eventType);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    private function createEventTemplateMessages(): bool
    {
        foreach (EventMessageInstaller::getRequiredEventTemplates() as $eventTemplateMessage) {
            $result = EventMessageInstaller::install($eventTemplateMessage);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    private function createUserFields(): bool
    {
        $userFields = FieldsInstaller::getRequiredUserFields();
        $fieldsInstaller = new FieldsInstaller();

        foreach ($userFields as $userField) {
            $res = $fieldsInstaller->install($userField);

            if (!$res) {
                return false;
            }
        }

        return true;
    }

    private function deleteEventTypes(): bool
    {
        foreach (EventTypeInstaller::getRequiredEventTypes() as $eventType) {
            $result = EventTypeInstaller::uninstall($eventType);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    private function deleteEventTemplateMessages(): bool
    {
        foreach (EventMessageInstaller::getRequiredEventTemplates() as $eventTemplateMessage) {
            $result = EventMessageInstaller::uninstall($eventTemplateMessage);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Удаляет все пользовательские поля у сущности CALENDAR_EVENT
     *
     * @return bool
     */
    private function deleteUserFields(): bool
    {
        $arFields = FieldsInstaller::getRequiredUserFields();

        return (new FieldsInstaller())->uninstall($arFields);
    }

    private function checkModule(string $moduleName): bool
    {
        if (!\Bitrix\Main\Loader::includeModule($moduleName)) {
            global $APPLICATION;

            $APPLICATION->ThrowException(
                Loc::getMessage(
                    'QSOFT_COMMITTEES_MODULE_INCLUDE_ERROR',
                    ['#MODULE_NAME#' => $moduleName]
                )
            );

            return false;
        }

        return true;
    }

    public function installFiles()
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

    protected function isLocal()
    {
        if (strpos($this->MODULE_PATH, 'local/modules') !== false) {
            return true;
        }

        return false;
    }

    public function unInstallFiles()
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
//            global $APPLICATION;
//            $APPLICATION->ThrowException($e->getMessage());
//
//            return false;
            return true;
        }

        return true;
    }

    private function checkRequiredPackages(): bool
    {
        global $APPLICATION;

        if (!class_exists(\Kafka\Consumer::class)) {
            $APPLICATION->ThrowException(Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_ERROR", ['#PACKAGE_NAME#' => 'nmred/kafka-php']));
            return false;
        }

        if (!class_exists(\Monolog\Logger::class)) {
            $APPLICATION->ThrowException(Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_ERROR", ['#PACKAGE_NAME#' => 'monolog/monolog']));
            return false;
        }

        return true;
    }

    public function registerModule(): void
    {
        RegisterModule($this->MODULE_ID);

        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler(
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

        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnPageStart',
            $this->MODULE_ID,
            Events::class,
            'registerHandlers'
        );
    }
}
