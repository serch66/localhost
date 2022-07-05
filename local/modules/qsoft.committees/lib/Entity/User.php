<?php


namespace Qsoft\Committees\Entity;

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Qsoft\Main\Handlers\Main;

class User
{
    public static function getEmailByIds(array $userIds): array
    {
        $email = [];

        $dbUsers = UserTable::getList(
            [
                'select' => array('ID', 'EMAIL'),
                'filter' => array('ID' => $userIds)
            ]
        );

        while ($user = $dbUsers->fetch()) {
            $email[$user['ID']] = $user['EMAIL'];
        }

        return $email;
    }

	public static function isActive(int $userId, array $arGroups = []): bool
	{
		if (!Loader::includeModule('qsoft.main')) {
			return false;
		}

		return Main::isActive($userId, $arGroups);
	}


    public static function getPathToPersonalCard(int $userId): string
    {
        return $_SERVER['HTTP_ORIGIN'] . '/company/personal/user/' . $userId . '/';
    }

    public static function getFullName(int $userId): string
    {
        $dbUser = UserTable::getList(
            [
                'select' => ['ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME'],
                'filter' => ['ID' => $userId]
            ]
        );

        if ($user = $dbUser->fetch()) {
            return \CUser::FormatName(\CSite::GetDefaultNameFormat(), $user);
        }

        return '';
    }
}
