<?
namespace Qsoft\Committees;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
    /**
     * Возвращает true, если вызвавший метод находится в папке local
     *
     * @return bool
     */
    public static function isLocal(): bool
    {
        return strpos(BASE_DIR, '/local/') !== false;
    }

    /**
     * Возвращает путь к скрипту static
     *
     * @return string
     */
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
