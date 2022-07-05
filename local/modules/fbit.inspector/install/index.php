<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class fbit_inspector extends CModule
{
    public $MODULE_ID = "fbit.inspector";

    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_PATH;

    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct ()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION      = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME        = Loc::getMessage("FBIT_INSP_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("FBIT_INSP_MODULE_DESCRIPTION");
        $this->PARTNER_NAME       = Loc::getMessage("FBIT_INSP_MODULE_PARTNER_NAME");
        $this->PARTNER_URI        = Loc::getMessage("FBIT_INSP_MODULE_PARTNER_URL");
    }


    public function DoInstall ()
    {
        $this->registerModule();
        $this->registerEvents();

        $this->prepareUsers();
    }

    public function registerModule (): void
    {
        RegisterModule($this->MODULE_ID);
        \Bitrix\Main\Config\Option::set($this->MODULE_ID, 'department_id', 2207);
    }

    public function registerEvents (): void
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnBeforeUserUpdate', $this->MODULE_ID, '\Fbit\Inspector\EventHandler', 'OnBeforeUserUpdate');
        $eventManager->registerEventHandler(
            'socialnetwork',
            'OnSocNetLogDestinationSearchUsers',
            $this->MODULE_ID,
            '\Fbit\Inspector\EventHandler',
            'OnSocNetLogDestinationSearchUsers'
        );
    }

    public function DoUninstall ()
    {
        $this->unregisterEvents();
        $this->unRegisterModule();
    }

    public function unRegisterModule ()
    {
        UnRegisterModule($this->MODULE_ID);
        COption::RemoveOption($this->MODULE_ID);
    }

    public function unregisterEvents (): void
    {
    }

    public function prepareUsers ()
    {
        self::maybeCreateExcludeUserField();

        CModule::IncludeModule('fbit.inspector');
        $Filter = new \Fbit\Inspector\Filter();
        $Filter->GetDeleteDepatments();

        $usersToPrepare = \Bitrix\Main\UserTable::query()
            ->addFilter('UF_DEPARTMENT', $Filter->DeleteDepatments)
            ->addSelect('ID')
            ->fetchAll();

        $cuser = new \CUser();
        foreach (array_column($usersToPrepare, 'ID') as $userId) {
            $cuser->Update($userId, ['UF_EXCLUDE_FROM_SELECTOR' => true]);
        }
    }

    public function maybeCreateExcludeUserField ()
    {
        $isFieldExist = (bool)\CUserTypeEntity::GetList([], [
            'FIELD_NAME' => 'UF_EXCLUDE_FROM_SELECTOR',
        ])->Fetch();
        if ($isFieldExist) {
            return;
        }
        $com = new \CUserTypeEntity();
        $com->Add([
            'ENTITY_ID'         => 'USER',
            'FIELD_NAME'        => 'UF_EXCLUDE_FROM_SELECTOR',
            'USER_TYPE_ID'      => 'boolean',
            'MULTIPLE'          => 'N',
            'SETTINGS'          => [
                'DEFAULT_VALUE'  => 0,
                'DISPLAY'        => 'DROPDOWN',
                'LABEL'          => [
                    '',
                    '',
                ],
                'LABEL_CHECKBOX' => '',
            ],
            'EDIT_FORM_LABEL'   => ['ru' => 'Исключить из поиска в соц. сети'],
            'LIST_COLUMN_LABEL' => ['ru' => 'Исключить из поиска в соц. сети'],
            'LIST_FILTER_LABEL' => ['ru' => 'Исключить из поиска в соц. сети'],
        ]);
    }
}
