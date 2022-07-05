<?php

namespace Sprint\Migration;


class Version20220210113908 extends Version
{
    protected $description = "Добавление пользовательского поля Юридический адрес";

    protected $moduleVersion = "3.27.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'CRM_LEAD',
  'FIELD_NAME' => 'UF_CRM_1643913369533',
  'USER_TYPE_ID' => 'address',
  'XML_ID' => NULL,
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'E',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SHOW_MAP' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Адрес юридический',
    'ru' => 'Адрес юридический',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Адрес юридический',
    'ru' => 'Адрес юридический',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Адрес юридический',
    'ru' => 'Адрес юридический',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => NULL,
    'ru' => NULL,
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => NULL,
    'ru' => NULL,
  ),
));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntity('USER', 'UF_CRM_1643913369533');
    }
}
