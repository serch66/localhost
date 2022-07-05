<?php

namespace Sprint\Migration;


class QSOFT_167283_20210211154734 extends Version
{
    protected $description = "добавление пользовательских полей UF_SID, UF_HASH, UF_UPDATE_DATE, UF_COMPANY_ID, UF_DEPARTMENT_ORG_ID, UF_DEPARTMENT_ORG_NAME, UF_DEPARTMENT_MNG_ID";

    protected $moduleVersion = "3.23.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
              'ENTITY_ID' => 'USER',
              'FIELD_NAME' => 'UF_SID',
              'USER_TYPE_ID' => 'string',
              'XML_ID' => '',
              'SORT' => '100',
              'MULTIPLE' => 'N',
              'MANDATORY' => 'N',
              'SHOW_FILTER' => 'Y',
              'SHOW_IN_LIST' => 'Y',
              'EDIT_IN_LIST' => 'Y',
              'IS_SEARCHABLE' => 'N',
              'SETTINGS' =>
                  array (
                      'SIZE' => 50,
                      'ROWS' => 1,
                      'REGEXP' => '',
                      'MIN_LENGTH' => 0,
                      'MAX_LENGTH' => 0,
                      'DEFAULT_VALUE' => '',
                  ),
              'EDIT_FORM_LABEL' =>
                  array (
                      'en' => 'SID',
                      'ru' => 'SID',
                  ),
              'LIST_COLUMN_LABEL' =>
                  array (
                      'en' => 'SID',
                      'ru' => 'SID',
                  ),
              'LIST_FILTER_LABEL' =>
                  array (
                      'en' => 'SID',
                      'ru' => 'SID',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
          'ENTITY_ID' => 'USER',
          'FIELD_NAME' => 'UF_HASH',
          'USER_TYPE_ID' => 'string',
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
                  'SIZE' => 50,
                  'ROWS' => 1,
                  'REGEXP' => '',
                  'MIN_LENGTH' => 0,
                  'MAX_LENGTH' => 0,
                  'DEFAULT_VALUE' => '',
              ),
          'EDIT_FORM_LABEL' =>
              array (
                  'en' => 'hash',
                  'ru' => 'hash',
              ),
          'LIST_COLUMN_LABEL' =>
              array (
                  'en' => 'hash',
                  'ru' => 'hash',
              ),
          'LIST_FILTER_LABEL' =>
              array (
                  'en' => 'hash',
                  'ru' => 'hash',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
              'ENTITY_ID' => 'USER',
              'FIELD_NAME' => 'UF_UPDATE_DATE',
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
                      'en' => 'Дата последнего изменения в ИС Персонал',
                      'ru' => 'Дата последнего изменения в ИС Персонал',
                  ),
              'LIST_COLUMN_LABEL' =>
                  array (
                      'en' => 'Дата последнего изменения в ИС Персонал',
                      'ru' => 'Дата последнего изменения в ИС Персонал',
                  ),
              'LIST_FILTER_LABEL' =>
                  array (
                      'en' => 'Дата последнего изменения в ИС Персонал',
                      'ru' => 'Дата последнего изменения в ИС Персонал',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
              'ENTITY_ID' => 'USER',
              'FIELD_NAME' => 'UF_COMPANY_ID',
              'USER_TYPE_ID' => 'integer',
              'XML_ID' => '',
              'SORT' => '100',
              'MULTIPLE' => 'N',
              'MANDATORY' => 'N',
              'SHOW_FILTER' => 'Y',
              'SHOW_IN_LIST' => 'Y',
              'EDIT_IN_LIST' => 'Y',
              'IS_SEARCHABLE' => 'N',
              'SETTINGS' =>
                  array (
                      'SIZE' => 20,
                      'MIN_VALUE' => 0,
                      'MAX_VALUE' => 0,
                      'DEFAULT_VALUE' => '',
                  ),
              'EDIT_FORM_LABEL' =>
                  array (
                      'en' => 'ID компании',
                      'ru' => 'ID компании',
                  ),
              'LIST_COLUMN_LABEL' =>
                  array (
                      'en' => 'ID компании',
                      'ru' => 'ID компании',
                  ),
              'LIST_FILTER_LABEL' =>
                  array (
                      'en' => 'ID компании',
                      'ru' => 'ID компании',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
              'ENTITY_ID' => 'USER',
              'FIELD_NAME' => 'UF_DEPARTMENT_ORG_ID',
              'USER_TYPE_ID' => 'integer',
              'XML_ID' => '',
              'SORT' => '100',
              'MULTIPLE' => 'N',
              'MANDATORY' => 'N',
              'SHOW_FILTER' => 'Y',
              'SHOW_IN_LIST' => 'Y',
              'EDIT_IN_LIST' => 'Y',
              'IS_SEARCHABLE' => 'N',
              'SETTINGS' =>
                  array (
                      'SIZE' => 20,
                      'MIN_VALUE' => 0,
                      'MAX_VALUE' => 0,
                      'DEFAULT_VALUE' => '',
                  ),
              'EDIT_FORM_LABEL' =>
                  array (
                      'en' => 'ID подразделения по штат. структуре',
                      'ru' => 'ID подразделения по штат. структуре',
                  ),
              'LIST_COLUMN_LABEL' =>
                  array (
                      'en' => 'ID подразделения по штат. структуре',
                      'ru' => 'ID подразделения по штат. структуре',
                  ),
              'LIST_FILTER_LABEL' =>
                  array (
                      'en' => 'ID подразделения по штат. структуре',
                      'ru' => 'ID подразделения по штат. структуре',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
                  'ENTITY_ID' => 'USER',
                  'FIELD_NAME' => 'UF_DEPARTMENT_ORG_NAME',
                  'USER_TYPE_ID' => 'string',
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
                          'SIZE' => 50,
                          'ROWS' => 1,
                          'REGEXP' => '',
                          'MIN_LENGTH' => 0,
                          'MAX_LENGTH' => 0,
                          'DEFAULT_VALUE' => '',
                      ),
                  'EDIT_FORM_LABEL' =>
                      array (
                          'en' => 'Название подразделения по штат. структуре',
                          'ru' => 'Название подразделения по штат. структуре',
                      ),
                  'LIST_COLUMN_LABEL' =>
                      array (
                          'en' => 'Название подразделения по штат. структуре',
                          'ru' => 'Название подразделения по штат. структуре',
                      ),
                  'LIST_FILTER_LABEL' =>
                      array (
                          'en' => 'Название подразделения по штат. структуре',
                          'ru' => 'Название подразделения по штат. структуре',
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
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
              'ENTITY_ID' => 'USER',
              'FIELD_NAME' => 'UF_DEPARTMENT_MNG_ID',
              'USER_TYPE_ID' => 'integer',
              'XML_ID' => '',
              'SORT' => '100',
              'MULTIPLE' => 'N',
              'MANDATORY' => 'N',
              'SHOW_FILTER' => 'Y',
              'SHOW_IN_LIST' => 'Y',
              'EDIT_IN_LIST' => 'Y',
              'IS_SEARCHABLE' => 'N',
              'SETTINGS' =>
                  array (
                      'SIZE' => 20,
                      'MIN_VALUE' => 0,
                      'MAX_VALUE' => 0,
                      'DEFAULT_VALUE' => '',
                  ),
              'EDIT_FORM_LABEL' =>
                  array (
                      'en' => 'ID подразделения по упр. структуре',
                      'ru' => 'ID подразделения по упр. структуре',
                  ),
              'LIST_COLUMN_LABEL' =>
                  array (
                      'en' => 'ID подразделения по упр. структуре',
                      'ru' => 'ID подразделения по упр. структуре',
                  ),
              'LIST_FILTER_LABEL' =>
                  array (
                      'en' => 'ID подразделения по упр. структуре',
                      'ru' => 'ID подразделения по упр. структуре',
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
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists(
            'USER',
            [
                'UF_SID',
                'UF_HASH',
                'UF_UPDATE_DATE',
                'UF_COMPANY_ID',
                'UF_DEPARTMENT_ORG_ID',
                'UF_DEPARTMENT_ORG_NAME',
                'UF_DEPARTMENT_MNG_ID'
            ]
        );
    }
}
