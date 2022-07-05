<?php

namespace CrmCustom;

class UserTypeInn extends \Bitrix\Main\UserField\Types\StringType
{
    public const
        USER_TYPE_ID = 'inn_field',
        RENDER_COMPONENT = 'fbit:main.field.inn';

    public static function getDescription(): array
    {
        return array(
            'DESCRIPTION' => 'Поле поиска ИНН',
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_STRING
        );
    }


}
