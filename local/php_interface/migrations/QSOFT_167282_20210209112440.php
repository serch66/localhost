<?php

namespace Sprint\Migration;


class QSOFT_167282_20210209112440 extends Version
{
    protected $description = "Добавление группы пользователей \"Доступ к порталу\"";

    protected $moduleVersion = "3.23.4";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();

        $helper->UserGroup()->saveGroup('portal_access',array (
            'ACTIVE' => 'Y',
            'C_SORT' => '100',
            'ANONYMOUS' => 'N',
            'NAME' => 'Доступ к порталу',
            'DESCRIPTION' => '',
            'SECURITY_POLICY' =>
                array (
                ),
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserGroup()->deleteGroup('portal_access');
    }
}
