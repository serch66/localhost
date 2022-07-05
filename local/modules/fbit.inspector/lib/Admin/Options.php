<?php

namespace Fbit\Inspector\Admin;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;

use const Fbit\Inspector\MODULE_ID;

Loc::loadLanguageFile(__FILE__);

class Options
{
    public static $optionDefaults;

    public static function getActiveUsersGroupId(): ?int
    {
        return (int)self::getOption('department_id');
    }

    /**
     * Метод сохраняет настройки модуля если тип запроса POST и прохождение csrf проверки было успешно
     * Предусмотрено два режима сохранения:
     * a) Как есть
     * б) Установить стандартные настройки
     *
     * @throws ArgumentOutOfRangeException
     */
    public function ensureOptionsSaveRequired(): void
    {
        if (!static::canWrite()) {
            return;
        }

        global $APPLICATION;
        $request = Context::getCurrent()->getRequest();

        if ($request->isPost() && check_bitrix_sessid()) {
            $tabs = static::getTabArray();

            foreach ($tabs as $tab) {
                foreach ($tab["OPTIONS"] as $option) {
                    if (!is_array($option)) {
                        continue;
                    }

                    if ($option["note"]) {
                        continue;
                    }

                    if ($request["apply"]) {
                        $optionValue = $request->getPost($option[0]);

                        if (in_array($option[0], self::getRequiredOptions()) && empty($optionValue)) {
                            Option::set(
                                MODULE_ID,
                                $option[0],
                                $option[2]
                            );
                        } else {
                            Option::set(
                                MODULE_ID,
                                $option[0],
                                is_array($optionValue) ? implode(",", $optionValue) : $optionValue
                            );
                        }
                    } elseif ($request["default"]) {
                        Option::set(MODULE_ID, $option[0], $option[2]);
                    }
                }
            }

            LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . MODULE_ID . "&lang=" . LANG);
        }
    }

    /**
     * Возвращает массив табов и их полей и секций для адмистративной части страницы настроек модуля
     *
     * @return array
     * @throws ArgumentOutOfRangeException
     */
    public static function getTabArray(): array
    {
        global $APPLICATION;
        self::setOptionDefaults();

        
        
        ob_start();
        /**
         * @var array $crmQueueSelected
         */

        if(is_null($crmQueueSelected)){
            $crmQueueSelected = [];
        }

        
        return [
            [
                'DIV' => 'main',
                'TAB' => Loc::getMessage('FBIT_INSP_OPTIONS_TAB_INSP_NAME'),
                'TITLE' => Loc::getMessage('FBIT_INSP_OPTIONS_TAB_INSP_TITLE'),
                'OPTIONS' => [
                    [
                        'department_id',
                        Loc::getMessage('FBIT_INSP_OPTIONS_DEPARTAMENT_ID'),
                        self::$optionDefaults['department_id'] ?: '',
                        [
                            'text',
                            '1'
                        ],
                    ],
                   
                ]
            ],
            [
                'DIV' => 'rights',
                'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
                'TITLE' => GetMessage('MAIN_TAB_TITLE_RIGHTS')
            ]
        ];
    }

    /**
     * Возвращает массив кодов полей, чьи значения не должны быть пустыми
     *
     * @return array
     */
    protected static function getRequiredOptions(): array
    {
        return [];
    }

    /**
     * Устанавливает в переменную класса значения по умолчанию для всех полей
     *
     * @throws ArgumentOutOfRangeException
     */
    public static function setOptionDefaults()
    {
        static::$optionDefaults = Option::getDefaults(MODULE_ID);
    }

    /**
     * Метод для получения PASSWORD для basic авторизации
     *
     * @param $optionName
     * @param string $default
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getOption($optionName, $default = ''): string
    {
        return Option::get(MODULE_ID, $optionName, $default);
    }


    protected static function getRight()
    {
        global $APPLICATION;
        return $APPLICATION->GetGroupRight(MODULE_ID);
    }

    public static function canRead()
    {
        return static::getRight() >= 'R';
    }

    public static function canWrite()
    {
        return static::getRight() >= 'W';
    }
}
