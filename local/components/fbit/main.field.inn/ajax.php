<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (!CModule::IncludeModule('crm'))
{
	return;
}
/*
 * ONLY 'POST' METHOD SUPPORTED
 * SUPPORTED ACTIONS:
 */
global $DB, $APPLICATION;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

if(!function_exists('__CrmCompanyDetailsEndHtmlResonse'))
{
	function __CrmCompanyDetailsEndHtmlResonse()
	{
		if(!defined('PUBLIC_AJAX_MODE'))
		{
			define('PUBLIC_AJAX_MODE', true);
		}
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
		die();
	}
}

if(!function_exists('__CrmCompanyDetailsEndJsonResonse'))
{
	function __CrmCompanyDetailsEndJsonResonse($result)
	{
		$GLOBALS['APPLICATION']->RestartBuffer();
		Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
		if(!empty($result))
		{
			echo CUtil::PhpToJSObject(['data' => $result]);
		}
		if(!defined('PUBLIC_AJAX_MODE'))
		{
			define('PUBLIC_AJAX_MODE', true);
		}
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
		die();
	}
}

if (!CCrmSecurityHelper::IsAuthorized() || !check_bitrix_sessid() || $_SERVER['REQUEST_METHOD'] != 'POST')
{
	return;
}

CUtil::JSPostUnescape();
$APPLICATION->RestartBuffer();
Header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);

$action = isset($_POST['ACTION']) ? $_POST['ACTION'] : '';
if($action === '' && isset($_POST['MODE']))
{
	$action = $_POST['MODE'];
}
if($action === '')
{
	__CrmCompanyDetailsEndJsonResonse(array('ERROR'=>'ACTION IS NOT DEFINED!'));
}
if($action === 'SEARCH_COMPANY')
{
    \Bitrix\Main\Loader::includeModule('fbit.soapserver');
    $result['items'] = [];
    $app = \Bitrix\Main\Application::getInstance();
    $request = $app->getContext()->getRequest();
    $inn = $request->getPost('searchINN');
    $kpp = $request->getPost('searchKPP');
    $entityid = $request->getPost('entityid');


    $requisiteEntity = new \Bitrix\Crm\EntityRequisite();
    $filter = [
        "RQ_INN" => $inn,
        "ENTITY_TYPE_ID" => \CCrmOwnerType::Company,
    ];

    if($kpp){
        $filter["RQ_KPP"] = $kpp;
    }
    $requisiteList = $requisiteEntity->getList([
        'select' => ['ID', 'RQ_INN', "RQ_KPP", "ENTITY_ID", 'ENTITY_TYPE_ID'],
        'filter' => $filter,
    ])->fetchAll();

    foreach ($requisiteList as $k => $requisite){
        $company = \CCrmCompany::GetListEx(
            [],
            [
                "=ID" => $requisite['ENTITY_ID'], 'CHECK_PERMISSIONS' => 'N'
            ],
            false,
            false,
            ["ID", "TITLE", "ASSIGNED_BY_NAME", "ASSIGNED_BY_LAST_NAME", "ASSIGNED_BY_SECOND_NAME",
                \Fbit\B24\ConverterCompany::CODEMDM_COMPANY_1C_FIELD,
                \Fbit\B24\ConverterCompany::STATUS_FIELD,
                \Fbit\B24\ConverterCompany::DEPARTMENT_FIELD,

            ],
        )->Fetch();

        $info = [];
        $info[] =  "{$company['TITLE']}({$company['ID']})";
        $devisionPrefix = $company[\Fbit\B24\ConverterCompany::DEPARTMENT_FIELD];
        if($devisionPrefix){
            $info[] =  "Префикс дивизиона " . $devisionPrefix;
        }

        $status = getEnumFields(\Fbit\B24\ConverterCompany::STATUS_FIELD, $company[\Fbit\B24\ConverterCompany::STATUS_FIELD]);
        if($status){
            $info[] =  "Статус " . $status ;
        }

        $couter = ++$k;
        $result['items'][] = "{$couter}. " . join('/', $info);
    }
	__CrmCompanyDetailsEndJsonResonse($result);
} else if($action === 'CODEMDM_COMPANY_UPDATE') {
    \Bitrix\Main\Loader::includeModule('fbit.soapserver');
    $result['items'] = [];
    $app = \Bitrix\Main\Application::getInstance();
    $request = $app->getContext()->getRequest();
    $inn = $request->getPost('searchINN');
    $kpp = $request->getPost('searchKPP');
    $entityid = $request->getPost('entityid');

    $requisiteEntity = new \Bitrix\Crm\EntityRequisite();
    $filter = [
        "RQ_INN" => $inn,
        "ENTITY_TYPE_ID" => \CCrmOwnerType::Company,
    ];

    if($kpp){
        $filter["RQ_KPP"] = $kpp;
    }

    $requisite = $requisiteEntity->getList([
        'select' => ['ID', 'RQ_INN', "RQ_KPP", "ENTITY_ID", 'ENTITY_TYPE_ID'],
        'filter' => $filter
    ])->fetch();

    $company = \CCrmCompany::GetListEx(
        [],
        [
            "=ID" => $requisite['ENTITY_ID'], 'CHECK_PERMISSIONS' => 'N'
        ],
        false,
        false,
        ["ID", "TITLE", "ASSIGNED_BY_NAME", "ASSIGNED_BY_LAST_NAME", "ASSIGNED_BY_SECOND_NAME",
            \Fbit\B24\ConverterCompany::CODEMDM_COMPANY_1C_FIELD,
            \Fbit\B24\ConverterCompany::STATUS_FIELD,
            \Fbit\B24\ConverterCompany::DEPARTMENT_FIELD,

        ],
        )->Fetch();


    if($company[\Fbit\B24\ConverterCompany::CODEMDM_COMPANY_1C_FIELD]){
        $result['items'][] = [
            \Fbit\B24\Lead\ConverterLead::CODEMDM_LEAD_FIELD => $company[\Fbit\B24\ConverterCompany::CODEMDM_COMPANY_1C_FIELD],
        ];
    }

    __CrmCompanyDetailsEndJsonResonse($result);
}


function createFullFio(string $name, string $lastname, string $secondname) : string {
    $fio = [];
    if($lastname){
        $fio[] = $lastname;
    }
    if($name){
        $fio[] = $name;
    }
    if($secondname){
        $fio[] = $secondname;
    }

    return join(' ', $fio);
}

function getEnumFields($fieldName, $fieldId){
    $dbUserFields = \Bitrix\Main\UserFieldTable::getList(array(
        'filter' => array('FIELD_NAME' => $fieldName)
    ))->fetch();
    if (!empty($dbUserFields) && $dbUserFields["USER_TYPE_ID"] == 'enumeration') {
        $Enum = \CUserFieldEnum::GetList(
            array(),
            array('USER_FIELD_ID' => $dbUserFields['ID'], "ID" => $fieldId)
        )->Fetch();

        if($Enum){
            return $Enum['VALUE'];
        }
    }
    return '';
}
