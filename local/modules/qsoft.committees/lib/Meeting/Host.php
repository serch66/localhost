<?php


namespace Qsoft\Committees\Meeting;


use Bitrix\Main\Localization\Loc;

class Host
{
    private static $login = 'meetingsHost';

    /**
     * Возвращает сгенерированный случайный пароль
     *
     * @return string
     * @throws \Exception
     */
    private static function getGeneratedHostPassword(): string
    {
        $password = \Bitrix\Main\Security\Random::getString(15);
        $password[random_int(0, 14)] = '!';

        return $password;
    }

    /**
     * Создает пользователя-организатора
     *
     * @return bool
     * @throws \Exception
     */
    public static function createHostUser(): bool
    {
        $cUser = new \CUser();
        $password = self::getGeneratedHostPassword();

        if (!self::hostUserExist()) {
            $id = $cUser->Add([
                'LOGIN' => 'meetingsHost',
                'NAME' => 'Организатор',
                'EMAIL' => 'meetingsHost@qsoft.ru',
                'PASSWORD' => $password,
                'CONFIRM_PASSWORD' => $password
            ]);

            if (!$id) {
                global $APPLICATION;

                $APPLICATION->ThrowException(Loc::getMessage('QSOFT_COMMITTEES_MEETING_HOST_ERROR_CREATE'));
                return false;
            }
        }

        return true;
    }

    public static function hostUserExist(): bool
    {
        $userInfo = \Bitrix\Main\UserTable::getList([
            'filter' => ['LOGIN' => 'meetingsHost', 'EMAIL' => 'meetingsHost@qsoft.ru'],
            'select' => ['ID']
        ])->fetch();

        return (bool)$userInfo;
    }

    /**
     * Удаляет пользователя-организатора
     *
     */
    public static function deleteHostUser(): bool
    {
        $userId = self::getId();

        if ($userId) {
            $res = \CUser::Delete($userId);

            if (!$res) {
                global $APPLICATION;
                $APPLICATION->ThrowException(Loc::getMessage('QSOFT_COMMITTEES_MEETING_HOST_ERROR_DELETE'));

                return false;
            }
        }

        return true;
    }
    /**
     * Возвращает id пользователя-организатора
     *
     * @return int
     */
    public static function getId(): int
    {
        $id = 0;

        $userInfo = self::getInfo(['ID']);

        if (!empty($userInfo)) {
            $id = $userInfo['ID'];
        }

        return $id;
    }

    /**
     * Возвращает имя пользователя-организатора
     *
     * @return string
     */
    public static function getName(): string
    {
        $name = '';

        $userInfo = self::getInfo(['NAME']);

        if (!empty($userInfo)) {
            $name = $userInfo['NAME'];
        }

        return $name;
    }

    /**
     * Возвращает логин пользователя-организатора
     *
     * @return string
     */
    public static function getLogin(): string
    {
        $login = '';

        $userInfo = self::getInfo(['LOGIN']);

        if (!empty($userInfo)) {
            $login = $userInfo['LOGIN'];
        }

        return $login;
    }

    /**
     * Возвращает информацию по пользователю-организатору
     *
     * @param $arSelect
     * @return array
     */
    public static function getInfo($arSelect): array
    {
        try {

            $userInfo = \Bitrix\Main\UserTable::getList([
                'filter' => ['LOGIN' => self::$login],
                'select' => $arSelect
            ])->fetch();

        } catch (\Throwable $e) {
            $userInfo = [];
        }

        return $userInfo ?: [];
    }
}
