<?php
use Bitrix\Main\Loader;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/custom/autoload.php';

// Полезные функции
Loader::includeModule('qsoft.main');
Loader::includeModule('qsoft.personal');
Loader::includeModule('qsoft.committees');

CModule::AddAutoloadClasses(
    '',
    array(
        '\CrmCustom\UserTypeInn' => '/local/php_interface/include/user_fields/usertypeinn.php',        
        '\CrmCustom\CrmActivityCall' => '/local/php_interface/include/helpers/crm_activity_call.php',
        '\CrmCustom\Lead' => '/local/php_interface/include/helpers/lead.php',
        '\CrmCustom\ImopenlineMessage' => '/local/php_interface/include/helpers/im_openline_message.php',
    )
);

AddEventHandler('main', 'OnUserTypeBuildList', array('\CrmCustom\UserTypeInn', 'GetUserTypeDescription'));



\Bitrix\Main\EventManager::getInstance()->addEventHandler("imopenlines", "OnImopenlineMessageReceive", Array("\CrmCustom\ImopenlineMessage", "OnImopenlineMessageReceive"));
