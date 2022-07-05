<?php

namespace Qsoft\Committees\Install;

use Bitrix\Main\Localization\Loc;

class FieldsInstaller
{
    private \CUserTypeEntity $cUserTypeEntity;

    public function __construct()
    {
        $this->cUserTypeEntity = new \CUserTypeEntity();
    }

    public function install($arFields): bool
    {
        if (!(bool)$this->userFieldExist($arFields['ENTITY_ID'], $arFields['FIELD_NAME'])) {
            $result = $this->cUserTypeEntity->Add($arFields);

            if (!$result) {
                global $APPLICATION;

                $APPLICATION->ThrowException(
                    Loc::getMessage(
                        'QSOFT_COMMITTEES_MODULE_INSTALL_ERROR_USER_FIELD',
                        ['#FIELD_CODE#' => $arFields['FIELD_NAME'], '#ENTITY_ID#' => $arFields['ENTITY_ID']]
                    )
                );

                return false;
            }
        }

        return true;
    }

    public function uninstall($arFields): bool
    {
        foreach ($arFields as $field) {

            $id = $this->userFieldExist($field['ENTITY_ID'], $field['FIELD_NAME']);

            if ($id !== 0) {
                $result = (new \CUserTypeEntity())->Delete($id)->result;

                if (!$result) {
                    global $APPLICATION;

                    $APPLICATION->ThrowException(
                        Loc::getMessage(
                            'QSOFT_COMMITTEES_MODULE_DELETE_ERROR_USER_FIELD',
                            ['#FIELD_CODE#' => $field['FIELD_NAME'], '#ENTITY_ID#' => $field['ENTITY_ID']]
                        )
                    );

                    return false;
                }
            }
        }

        return true;
    }

    private function userFieldExist(string $entityId, string $fieldName): int
    {
        $id = 0;

        $result = \CUserTypeEntity::GetList(
            [],
            [
                'ENTITY_ID' => $entityId,
                'FIELD_NAME' => $fieldName
            ]
        )->Fetch();

        if ($result) {
            $id = $result['ID'];
        }

        return $id;
    }

    public static function getRequiredUserFields(): array
    {
        return
            [
            /**
             * Пользовательское поле UF_COMMITTEE_EVENT_ID у сущности CALENDAR_EVENT
             */
            [
                'ENTITY_ID' => 'CALENDAR_EVENT',
                'FIELD_NAME' => 'UF_COMMITTEE_EVENT_ID',
                'USER_TYPE_ID' => 'integer',
                'XML_ID' => 'UF_COMMITTEE_EVENT_ID',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'MIN_VALUE' => 0,
                        'MAX_VALUE' => 0,
                        'DEFAULT_VALUE' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_COMMITTEE_EVENT_ID'),
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_COMMITTEE_EVENT_ID'),
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '',
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_COMMITTEE_EVENT_ID'),
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_COMMITTEE_EVENT_ID'),
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => '',
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_COMMITTEE_EVENT_ID'),
                    ),
            ],

            /**
             * Пользовательское поле UF_PREV_DATE_TO у сущности CALENDAR_EVENT
             */
            [
                'ENTITY_ID' => 'CALENDAR_EVENT',
                'FIELD_NAME' => 'UF_PREV_DATE_TO',
                'USER_TYPE_ID' => 'datetime',
                'XML_ID' => '',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'DEFAULT_VALUE' =>
                            array (
                                'TYPE' => 'NONE',
                                'VALUE' => '',
                            ),
                        'USE_SECOND' => 'Y',
                        'USE_TIMEZONE' => 'N',
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_TO"),
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_UF_TRACK_HELP"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_UF_TRACK_HELP"),
                    ),
            ],

            /**
             * Пользовательское поле UF_PREV_DATE_FROM у сущности CALENDAR_EVENT
             */
            [
                'ENTITY_ID' => 'CALENDAR_EVENT',
                'FIELD_NAME' => 'UF_PREV_DATE_FROM',
                'USER_TYPE_ID' => 'datetime',
                'XML_ID' => '',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'DEFAULT_VALUE' =>
                            array (
                                'TYPE' => 'NONE',
                                'VALUE' => '',
                            ),
                        'USE_SECOND' => 'Y',
                        'USE_TIMEZONE' => 'N',
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'en' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                        'ru' => Loc::getMessage("QSOFT_COMMITTEES_MODULE_INSTALL_PREV_DATE_FROM"),
                    ),
            ],

            /**
             * Пользовательское поле UF_INTERSECTIONS_CHECKED у сущности CALENDAR_EVENT
             */
            [
                'ENTITY_ID' => 'CALENDAR_EVENT',
                'FIELD_NAME' => 'UF_INTERSECTIONS_CHECKED',
                'USER_TYPE_ID' => 'boolean',
                'XML_ID' => '',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'DEFAULT_VALUE' => 1,
                        'DISPLAY' => 'CHECKBOX',
                        'LABEL' =>
                            array (
                                0 => '',
                                1 => '',
                            ),
                        'LABEL_CHECKBOX' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_INTERSECTIONS_CHECKED'),
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
            ],
            /**
             * Пользовательское поле UF_WAS_NOTIFIED у сущности CALENDAR_EVENT
             */
            [
                'ENTITY_ID' => 'CALENDAR_EVENT',
                'FIELD_NAME' => 'UF_WAS_NOTIFIED',
                'USER_TYPE_ID' => 'boolean',
                'XML_ID' => '',
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array (
                        'DEFAULT_VALUE' => 0,
                        'DISPLAY' => 'CHECKBOX',
                        'LABEL' =>
                            array (
                                0 => '',
                                1 => '',
                            ),
                        'LABEL_CHECKBOX' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                    ),
                'LIST_COLUMN_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                    ),
                'LIST_FILTER_LABEL' =>
                    array (
                        'en' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                        'ru' => Loc::getMessage('QSOFT_COMMITTEES_MODULE_INSTALL_USER_NOTIFIED_ABOUT_EXCLUSION'),
                    ),
                'ERROR_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
                'HELP_MESSAGE' =>
                    array (
                        'en' => '',
                        'ru' => '',
                    ),
            ]
        ];
    }
}