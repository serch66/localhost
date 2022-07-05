<?php

namespace Sprint\Migration;


class Version20211107162122 extends Version
{
    protected $description = "Добавление\\удаление пользовательских полей для сущности лид";

    protected $moduleVersion = "3.27.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1631777167',
                'USER_TYPE_ID' => 'employee',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1631779007',
                'USER_TYPE_ID' => 'iblock_element',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'DISPLAY' => 'LIST',
                        'LIST_HEIGHT' => 1,
                        'IBLOCK_ID' => 'lists:CLIENT_TYPE',
                        'DEFAULT_VALUE' => '',
                        'ACTIVE_FILTER' => 'N',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1631781934',
                'USER_TYPE_ID' => 'double',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'PRECISION' => 2,
                        'SIZE' => 20,
                        'MIN_VALUE' => 0.0,
                        'MAX_VALUE' => 0.0,
                        'DEFAULT_VALUE' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1632399667255',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510739993',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Сокращенное наименование организации',
                        'ru' => 'Сокращенное наименование организации',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Сокращенное наименование организации',
                        'ru' => 'Сокращенное наименование организации',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Сокращенное наименование организации',
                        'ru' => 'Сокращенное наименование организации',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510763585',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Полное наименование организации',
                        'ru' => 'Полное наименование организации',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Полное наименование организации',
                        'ru' => 'Полное наименование организации',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Полное наименование организации',
                        'ru' => 'Полное наименование организации',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510782273',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'ОГРН',
                        'ru' => 'ОГРН',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'ОГРН',
                        'ru' => 'ОГРН',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'ОГРН',
                        'ru' => 'ОГРН',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510830525',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Дата государственной регистрации',
                        'ru' => 'Дата государственной регистрации',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Дата государственной регистрации',
                        'ru' => 'Дата государственной регистрации',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Дата государственной регистрации',
                        'ru' => 'Дата государственной регистрации',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510849969',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'ОКПО',
                        'ru' => 'ОКПО',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'ОКПО',
                        'ru' => 'ОКПО',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'ОКПО',
                        'ru' => 'ОКПО',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510865688',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'ОКТМО',
                        'ru' => 'ОКТМО',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'ОКТМО',
                        'ru' => 'ОКТМО',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'ОКТМО',
                        'ru' => 'ОКТМО',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510881417',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Ген. Директор',
                        'ru' => 'Ген. Директор',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Ген. Директор',
                        'ru' => 'Ген. Директор',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Ген. Директор',
                        'ru' => 'Ген. Директор',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510895433',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Гл. бухгалтер',
                        'ru' => 'Гл. бухгалтер',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Гл. бухгалтер',
                        'ru' => 'Гл. бухгалтер',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Гл. бухгалтер',
                        'ru' => 'Гл. бухгалтер',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510913674',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Адрес фактический/юридический',
                        'ru' => 'Адрес фактический/юридический',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Адрес фактический/юридический',
                        'ru' => 'Адрес фактический/юридический',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Адрес фактический/юридический',
                        'ru' => 'Адрес фактический/юридический',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510933369',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Наименование банка',
                        'ru' => 'Наименование банка',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Наименование банка',
                        'ru' => 'Наименование банка',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Наименование банка',
                        'ru' => 'Наименование банка',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510956017',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'БИК',
                        'ru' => 'БИК',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'БИК',
                        'ru' => 'БИК',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'БИК',
                        'ru' => 'БИК',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510972907',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Расчетный счет',
                        'ru' => 'Расчетный счет',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Расчетный счет',
                        'ru' => 'Расчетный счет',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Расчетный счет',
                        'ru' => 'Расчетный счет',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633510988625',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Кор. счет',
                        'ru' => 'Кор. счет',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Кор. счет',
                        'ru' => 'Кор. счет',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Кор. счет',
                        'ru' => 'Кор. счет',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633511003281',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Валюта счета',
                        'ru' => 'Валюта счета',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Валюта счета',
                        'ru' => 'Валюта счета',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Валюта счета',
                        'ru' => 'Валюта счета',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633511018153',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Адрес банка',
                        'ru' => 'Адрес банка',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Адрес банка',
                        'ru' => 'Адрес банка',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Адрес банка',
                        'ru' => 'Адрес банка',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633511034000',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'SWIFT',
                        'ru' => 'SWIFT',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'SWIFT',
                        'ru' => 'SWIFT',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'SWIFT',
                        'ru' => 'SWIFT',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
            'ENTITY_ID' => 'CRM_LEAD',
            'FIELD_NAME' => 'UF_CRM_INN',
            'USER_TYPE_ID' => 'inn_field',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'E',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'Y',
            'SETTINGS' =>
                array (
                    'SIZE' => 225,
                    'ROWS' => 1,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array (
                    'en' => 'ИНН',
                    'ru' => 'ИНН',
                ),
            'LIST_COLUMN_LABEL' =>
                array (
                    'en' => 'ИНН',
                    'ru' => 'ИНН',
                ),
            'LIST_FILTER_LABEL' =>
                array (
                    'en' => 'ИНН',
                    'ru' => 'ИНН',
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
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD',
                'FIELD_NAME' => 'UF_CRM_1633688868',
                'USER_TYPE_ID' => 'iblock_element',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'DISPLAY' => 'LIST',
                        'LIST_HEIGHT' => 1,
                        'IBLOCK_ID' => 'lists:DEPARTMENT',
                        'DEFAULT_VALUE' => '',
                        'ACTIVE_FILTER' => 'N',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Отдел продаж',
                        'ru' => 'Отдел продаж',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Отдел продаж',
                        'ru' => 'Отдел продаж',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Отдел продаж',
                        'ru' => 'Отдел продаж',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD_SPD',
                'FIELD_NAME' => 'UF_CRM_61541465DD7DE',
                'USER_TYPE_ID' => 'employee',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Автор',
                        'ru' => 'Автор',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD_SPD',
                'FIELD_NAME' => 'UF_CRM_615414663C727',
                'USER_TYPE_ID' => 'iblock_element',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'DISPLAY' => 'LIST',
                        'LIST_HEIGHT' => 1,
                        'IBLOCK_ID' => 'lists:CLIENT_TYPE',
                        'DEFAULT_VALUE' => '',
                        'ACTIVE_FILTER' => 'N',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'Тип клиента',
                        'ru' => 'Тип клиента',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD_SPD',
                'FIELD_NAME' => 'UF_CRM_615414665B4F2',
                'USER_TYPE_ID' => 'double',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'PRECISION' => 2,
                        'SIZE' => 20,
                        'MIN_VALUE' => 0.0,
                        'MAX_VALUE' => 0.0,
                        'DEFAULT_VALUE' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'ИНН',
                        'ru' => 'ИНН',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'ИНН',
                        'ru' => 'ИНН',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'ИНН',
                        'ru' => 'ИНН',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD_SPD',
                'FIELD_NAME' => 'UF_CRM_6154146670876',
                'USER_TYPE_ID' => 'double',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'N',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'PRECISION' => 2,
                        'SIZE' => 20,
                        'MIN_VALUE' => 0.0,
                        'MAX_VALUE' => 0.0,
                        'DEFAULT_VALUE' => '',
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => 'КПП',
                        'ru' => 'КПП',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            array(
                'ENTITY_ID' => 'CRM_LEAD_SPD',
                'FIELD_NAME' => 'UF_CRM_6154146693B7B',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => null,
                'SORT' => '100',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'E',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' =>
                    array(
                        'SIZE' => 20,
                        'ROWS' => 1,
                        'REGEXP' => '',
                        'MIN_LENGTH' => 0,
                        'MAX_LENGTH' => 0,
                        'DEFAULT_VALUE' => null,
                    ),
                'EDIT_FORM_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'LIST_COLUMN_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'LIST_FILTER_LABEL' =>
                    array(
                        'en' => '1С_DOCUMENT_LINK',
                        'ru' => '1С_DOCUMENT_LINK',
                    ),
                'ERROR_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
                'HELP_MESSAGE' =>
                    array(
                        'en' => null,
                        'ru' => null,
                    ),
            )
        );
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1631777167');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1631779007');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1631781934');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1632399667255');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510739993');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510763585');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510782273');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510830525');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510849969');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510865688');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510881417');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510895433');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510913674');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510933369');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510956017');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510972907');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633510988625');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633511003281');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633511018153');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633511034000');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_INN');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD', 'UF_CRM_1633688868');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD_SPD', 'UF_CRM_61541465DD7DE');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD_SPD', 'UF_CRM_615414663C727');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD_SPD', 'UF_CRM_615414665B4F2');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD_SPD', 'UF_CRM_6154146670876');
        $helper->UserTypeEntity()->deleteUserTypeEntity('CRM_LEAD_SPD', 'UF_CRM_6154146693B7B');
    }
}
