<?php

namespace Qsoft\Main\Handlers;

use \Qsoft\Main\Helpers;

class Ldap
{
    public static function onLdapBeforeSync(&$arParams)
    {
        if (!defined('LDAP_SYNC')) {
            define('LDAP_SYNC', true);
        }
    }

    public static function onLdapUserFields($arFields)
    {
        $arBitrixUser = &$arFields[0];
        $arLdapUser = &$arFields[1];

        if (!empty($arLdapUser['objectsid'])) {
            $arBitrixUser['UF_SID'] = Helpers\Ldap::encodeSid($arLdapUser['objectsid']);
        }

        if (!$arBitrixUser['EMAIL']) {
            $arBitrixUser['EMAIL'] = $arLdapUser['samaccountname'] . '@efko.ru';
        }
    }
}
