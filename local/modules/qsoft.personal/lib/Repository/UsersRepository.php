<?php
namespace Qsoft\Personal\Repository;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Qsoft\Personal\Admin\Options;

/**
 * Class UsersRepository
 * @package Qsoft\Personal\Repository
 */
class UsersRepository extends ListRepository
{
    protected function getUrl(): string
    {
        $options = Options::getInfo(
            "method_users_path",
            "method_users_type",
            "GET"
        );

        return $options['url'];
    }

    public static function updateBySid(string $sid, array $fields): bool
    {
        $arUser = self::prepareFields($fields);
        $rsUser = UserTable::getList(
            [
                'filter' => ['=UF_SID' => $sid],
                'select' => array_merge(
                    array_keys($arUser),
                    ['ID', 'XML_ID']
                )
            ]
        );

        if ($arCurrent = $rsUser->fetch()) {
            if (!$arCurrent['XML_ID']) {
                $repPhotos = new PhotosRepository();
                $photo = $repPhotos->getById((int)$fields['id']);
                if ($photo) {
                    $arUser['PERSONAL_PHOTO'] = \CFile::MakeFileArray($photo);
                }
            }

            foreach ($arUser as $key => $value) {
                if (
                    $value instanceof Date ||
                    $value instanceof DateTime
                ) {
                    $value = $value->toString();
                }

                if ($value === $arCurrent[$key]) {
                    unset($arUser[$key]);
                }
            }

            if (empty($arUser)) {
                return true;
            }

            $user = new \CUser();
            if (!$user->Update($arCurrent['ID'], $arUser, false)) {
                throw new \Exception($user->LAST_ERROR);
            }

            if (file_exists($photo)) {
                @unlink($photo);
            }

            return true;
        }

        return false;
    }

    public static function update(int $userId, array $fields): bool
    {
        $arUser = self::prepareFields($fields);
        $user = new \CUser();
        if (!$user->Update($userId, $arUser, false)) {
            throw new \Exception($user->LAST_ERROR);
        }

        return true;
    }

    protected static function prepareFields(array $fields): array
    {
        if (!empty($fields['birthday'])) {
            $birthdayTime = strtotime($fields['birthday']);
            $birthday = \ConvertTimeStamp($birthdayTime);
        } else {
            $birthday = '';
        }

        $active = intval($fields['is_deleted']) === 0 &&
            intval($fields['is_fired']) === 0;

        $directionId = (int)$fields['division_mng_id'];

        $arUser = [
            'ACTIVE' => $active  ? 'Y' : 'N',
            'EMAIL' => $fields['email'],
            'NAME' => $fields['name'] ?? '',
            'SECOND_NAME' => $fields['patronymic'] ?? '',
            'LAST_NAME' => $fields['surname'] ?? '',
            'PERSONAL_GENDER' => '',
            'PERSONAL_BIRTHDAY' => $birthday,
            'WORK_POSITION' => $fields['position_mng_name'] ?? '',
            'PERSONAL_MOBILE' => $fields['phone_private'] ?? '',
            'WORK_PHONE' => $fields['phone_service'] ?? '',
            'WORK_COMPANY' => $fields['company_name'] ?? '',
            'WORK_DEPARTMENT' => $fields['division_mng_name'] ?? '',
            'XML_ID' => $fields['id'],
            'UF_UPDATE_DATE' => \ConvertTimeStamp($fields['dt_update']),
            'UF_COMPANY_ID' => $fields['company_id'] ?? '',
            'UF_DEPARTMENT_ORG_ID' => $fields['division_id'] ?? '',
            'UF_DEPARTMENT_ORG_NAME' => $fields['division_name'] ?? '',
            'UF_DEPARTMENT_MNG_ID' => $directionId ?? '',
            'UF_HASH' => $fields['hash'],
        ];

        if ($fields['sex']) {
            $arUser['PERSONAL_GENDER'] = $fields['sex'] == 2 ? 'F' : 'M';
        }

        if ($fields['photo']) {
            $arUser['PERSONAL_PHOTO'] = \CFile::MakeFileArray($fields['photo']);
        }

        $sections = DirectionsRepository::getAllSections();
        $arUser['UF_DEPARTMENT'] = [];

        if ($directionId) {
            if (isset($sections[$directionId])) {
                $arUser['UF_DEPARTMENT'] = [$sections[$directionId]['ID']];
            }
        }

        if (empty($arUser['UF_DEPARTMENT'])) {
            $directionId = (int)Options::getDefaultDirectionId();
            if (isset($sections[$directionId])) {
                $arUser['UF_DEPARTMENT'] = [$sections[$directionId]['ID']];
            }
        }

        return $arUser;
    }
}
