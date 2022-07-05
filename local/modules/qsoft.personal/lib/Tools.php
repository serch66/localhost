<?
namespace Qsoft\Personal;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
    public static function isLocal(): bool
    {
        return strpos(BASE_DIR, '/local/') !== false;
    }

    public static function getPathForStatic(): string
    {
        static $return = '';

        if (empty($return)) {
            if (self::isLocal()) {
                $return = '/local/static';
            } else {
                $return = '/bitrix';
            }
        }

        return $return;
    }
}
