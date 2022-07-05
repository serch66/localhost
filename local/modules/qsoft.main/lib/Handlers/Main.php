<?php

namespace Qsoft\Main\Handlers;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

use Qsoft\Main\Admin\Options;


class Main
{
    public static function onBeforeProlog()
    {
        $pathDav = '/bitrix/groupdav.php';
        $request = Context::getCurrent()->getRequest();
        if (
            strpos($request->getRequestUri(), $pathDav . '/') !== false
        ) {
            $uri = new Uri($request->getRequestUri());
            $_SERVER['PATH_INFO'] = str_replace($pathDav, '', $uri->getPath());
            include (Application::getDocumentRoot() . '/bitrix/groupdav.php');
        }
    }

    public static function onBeforeUserLogin($arParams)
    {
        if (empty($arParams['LOGIN'])) {
            return true;
        }

        $rsUser = UserTable::getList(
            [
                'filter' => ['=LOGIN' => $arParams['LOGIN']],
                'select' => ['ID']
            ]
        );
/*
        if ($arUser = $rsUser->fetch()) {
            if (!self::isActive($arUser['ID'])) {
                global $APPLICATION;
                $APPLICATION->ThrowException(Loc::getMessage('QSOFT_MAIN_HANDLERS_MAIN_NOT_ACCESS'));
                return false;
            }
        }*/

        return true;
    }

    public static function onAfterUserAdd($arFields)
    {
        if(!$arFields['ID'])
            return;

        if (!is_array($arFields['GROUP_ID']))
            return;

        self::checkActive(
            (int)$arFields['ID'],
            self::prepareGroups($arFields['GROUP_ID'])
        );
    }

    public static function onAfterUserUpdate($arFields)
    {
        if(!$arFields['ID'])
            return;

        if (!is_array($arFields['GROUP_ID']))
            return;

	    global $CACHE_MANAGER;
	    $CACHE_MANAGER->ClearByTag('is_user_' . $arFields['ID'] . '_active');

        self::checkActive(
            (int)$arFields['ID'],
            self::prepareGroups($arFields['GROUP_ID'])
        );
    }

    public static function onAfterSetUserGroup($userId, $arGroups)
    {
        if(!defined('LDAP_SYNC'))
            return;

        $activeUsersGroupId = Options::getActiveUsersGroupId();
        if (!$activeUsersGroupId)
            return;

        if (isset($arGroups[$activeUsersGroupId])) {
            $groups = $arGroups;
        } else {
            $groups = UserTable::getUserGroupIds($userId);
        }

        self::checkActive(
            (int)$userId,
            self::prepareGroups($groups)
        );
    }

    public static function isActive(int $userId, array $arGroups = []): bool
    {
	    global $CACHE_MANAGER;

	    $obCache = new \CPHPCache();
	    $cacheId = 'is_user_' . $userId . '_active';
	    $cacheDir = 'user_' . $userId . '_active_check';

	    $activeUsersGroupId = Options::getActiveUsersGroupId();
	    if (!$activeUsersGroupId) {
		    return false;
	    }

	    if ($obCache->InitCache(36000, $cacheId, $cacheDir)) {
		    $result = $obCache->GetVars()['ITEMS'];
	    } else {
		    $CACHE_MANAGER->StartTagCache($cacheDir);
		    $CACHE_MANAGER->RegisterTag($cacheId);

		    if (empty($arGroups)) {
			    $arGroups = UserTable::getUserGroupIds($userId);
			    $arGroups = self::prepareGroups($arGroups);
		    }

		    $result = in_array($activeUsersGroupId, $arGroups);

		    $CACHE_MANAGER->EndTagCache();
		    if ($obCache->StartDataCache(36000, $cacheId, $cacheDir)) {
			    $obCache->EndDataCache(['ITEMS' => $result]);
		    }
	    }

	    return $result;
    }

    protected static function checkActive(int $userId, array $arGroups = [])
    {
        if (
            $userId &&
            !self::isActive($userId, $arGroups)
        ) {
            Application::getConnection()->query('UPDATE b_user SET LAST_LOGIN = NULL WHERE ID=' . $userId);
        }
    }

    protected static function prepareGroups(array $arGroups): array
    {
        $groupIds = [];

        foreach ($arGroups as $group) {
            if (is_array($group)) {
                $groupIds[] = (int)$group['GROUP_ID'];
            } else {
                $groupIds[] = (int)$group;
            }
        }

        return $groupIds;
    }
    static function OnBeforeUserAddHandler(&$User){
        
       
        
        $ModuleId = 'extranet';
        
        
        
        if(!Loader::includeModule($ModuleId))
            return true;
        
        $GroupExtranetID =  Option::get($ModuleId, 'extranet_group', 0);
            
        if($GroupExtranetID <= 0)
            return true;
        
            
        if(in_array($GroupExtranetID, $User['GROUP_ID']) === false )
            return true;        
            
            
        $activeUsersGroupId = Options::getActiveUsersGroupId();
        if(in_array($activeUsersGroupId, $User['GROUP_ID']) !== false )
            return true;    
        
        $User['GROUP_ID'][] = $activeUsersGroupId;
        
        return true; 
    }
}
