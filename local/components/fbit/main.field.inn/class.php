<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Component\BaseUfComponent;
use Bitrix\Main\UserField\Types\StringType;

/**
 * Class IntegerUfComponent
 */
class IntegerUfComponent extends BaseUfComponent
{
	protected static function getUserTypeId(): string
	{
		return StringType::USER_TYPE_ID;
	}

	function getRequsites():array
    {
        return [
            'RQ_INN' => 'UF_CRM_INN',
            'RQ_KPP' => 'UF_CRM_1631781934',
            'RQ_COMPANY_NAME' => 'UF_CRM_1633510739993',
            'RQ_COMPANY_FULL_NAME' => 'UF_CRM_1633510763585',
            'RQ_OGRN' => 'UF_CRM_1633510782273',
            'RQ_COMPANY_REG_DATE' => 'UF_CRM_1633510830525',
            'RQ_DIRECTOR' => 'UF_CRM_1633510881417',
            'RQ_ADDR' => 'UF_CRM_1642686994701',
            'RQ_ADDR_UR' => 'UF_CRM_1643913369533',
        ];
    }

}
