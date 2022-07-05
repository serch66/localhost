<?php

namespace Qsoft\Committees\Admin;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\GroupTable;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

use Qsoft\Committees\Service\Intersections;

use const Qsoft\Committees\MODULE_ID;
use const Qsoft\Committees\BASE_DIR;

Loc::loadLanguageFile(__FILE__);

class Options
{
    public static $optionDefaults;
    public const MODULE_ID = MODULE_ID;

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
                        global $CACHE_MANAGER;
                        $CACHE_MANAGER->ClearByTag('notif_only_active_option');

                        if (in_array($option[0], self::getRequiredOptions()) && empty($optionValue)) {
                               Option::set(MODULE_ID,
                                   $option[0],
                                   $option[2]
                               );
                        } else {
                            Option::set(MODULE_ID,
                                $option[0],
                                is_array($optionValue) ? implode(",", $optionValue) : $optionValue
                            );

                            if ($option[0] === 'notify_intersections') {
                                if ($optionValue === 'Y') {
                                    Intersections::activate();
                                } else {
                                    Intersections::deactivate();
                                }
                            }
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
        self::setOptionDefaults();

        return [
            [
                'DIV'     => 'main',
                'TAB'     => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_TAB_MAIN_NAME'),
                'TITLE'   => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_TAB_MAIN_TITLE'),
                'OPTIONS' => [
                    [
                        'api_method_uri',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_METHOD_URI'),
                        self::$optionDefaults['api_method_uri'] ?: '',
                        ['text']
                    ],
                    [
                        'x-api-key',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_X_API_KEY'),
                        self::$optionDefaults['x_api_key'] ?: '',
                        ['text']
                    ],
                    [
                        'path_to_log',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_PATH_TO_LOG'),
                        self::$optionDefaults['path_to_log'] ?: '',
                        ['text']
                    ],
                    [
                        'link_template',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_EVENT_ETERNAL_LINK'),
                        self::$optionDefaults['link_template'] ?: '',
                        ['text']
                    ],
                    [
                        'notify_intersections',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_NOTIFY_INTERSECTIONS'),
                        self::$optionDefaults['notify_intersections'] ?: '',
                        ['checkbox']
                    ],
	                [
		                'notify_only_active_users',
		                Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_NOTIFY_ONLY_ACTIVE_USERS'),
		                self::$optionDefaults['notify_only_active_users'] ?: '',
		                ['checkbox']
	                ]
                ]
            ],
            [
                'DIV' => 'kafka',
                'TAB' => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_KAFKA_TAB'),
                'TITLE' => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_KAFKA_TITLE'),
                'OPTIONS' => [
                    [
                        'kafka_address',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_KAFKA_ADDRESS'),
                        self::$optionDefaults['kafka_address'] ?: '',
                        ['text']
                    ],
                    [
                        'kafka_port',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_KAFKA_PORT'),
                        self::$optionDefaults['kafka_port'] ?: '',
                        ['text']
                    ],
                    [
                        'kafka_topic',
                        Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_KAFKA_TOPIC'),
                        self::$optionDefaults['kafka_topic'] ?: '',
                        ['text']
                    ]
                ]
            ],
            [
                'DIV' => 'upload',
                'TAB' => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_MANUAL_UPLOAD_TAB'),
                'TITLE' => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_MANUAL_UPLOAD_TITLE'),
                'OPTIONS' => [
                    [
                        "upload_date_start",
                        Loc::getMessage("QSOFT_COMMITTEES_OPTIONS_UPLOAD_DATE_START"),
                        self::$optionDefaults['upload_date_start'] ?: "",
                        ["text"]
                    ],
                    [
                        "upload_date_end",
                        Loc::getMessage("QSOFT_COMMITTEES_OPTIONS_UPLOAD_DATE_END"),
                        self::$optionDefaults['upload_date_end'] ?: "",
                        ["text"]
                    ],
                    [
                        "page_number",
                        Loc::getMessage("QSOFT_COMMITTEES_OPTIONS_UPLOAD_PAGE_NUMBER"),
                        self::$optionDefaults['page_number'] ?: "",
                        ["text"]
                    ],
                    [
                        "count_on_page",
                        Loc::getMessage("QSOFT_COMMITTEES_OPTIONS_UPLOAD_COUNT_ON_PAGE"),
                        self::$optionDefaults['count_on_page'] ?: "",
                        ["text"]
                    ],
                    [
                        "is_uploading",
                        Loc::getMessage("QSOFT_COMMITTEES_OPTIONS_MANUAL_UPLOAD_IS_RUNNING"),
                        self::$optionDefaults['is_uploading'] ?: null,
                        ["checkbox"]
                    ],
                ],
                "AFTER_HTML" => self::getSyncBlock()
            ],
        ];
    }

    /**
     * Возвращает блок прогресс-бара синхронизации
     *
     * @return string
     */
    protected static function getSyncBlock(): string
    {
        \CJSCore::Init(['qsoft_committees_sync', 'ui']);
        Extension::load('ui.progressbar');
        Extension::load("ui.alerts");
        Extension::load("ui.buttons");
        Extension::load("ui.buttons.icons");

        ob_start();
        include BASE_DIR . '/view/sync.php';
        return ob_get_clean();
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
     * Метод для получения опции модуля "Qsoft: Комитеты"
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

    public static function getOptionsWithoutCache(array $options): array
    {
	    $result = [];
	    $con = \Bitrix\Main\Application::getConnection();
	    $sqlHelper = $con->getSqlHelper();

	    $query =
		    "SELECT NAME, VALUE 
			FROM b_option 
			WHERE `MODULE_ID` = '" .
		    $sqlHelper->forSql(MODULE_ID) .
		    "'"
	    ;

	    if (!empty($options)) {

		    $query .= " AND `NAME` IN (";

		    $options = array_map(
			    static function($el) {
				    return "'" . $el . "'";
			    },
			    $options
		    );

		    $query .= implode(', ', $options);
		    $query .= ")";
	    }

	    $res = $con->query($query);

	    while ($ar = $res->fetch())
	    {
		    $result[$ar["NAME"]] = $ar["VALUE"];
	    }

	    return $result;
    }

    public static function getNotifOnlyActiveOption(): string
    {
	    global $CACHE_MANAGER;

	    $obCache = new \CPHPCache();
	    $cacheId = 'notif_only_active_option';
	    $cacheDir = 'options.notif_active';

	    if ($obCache->InitCache(36000, $cacheId, $cacheDir)) {
		    $result = $obCache->GetVars()['ITEMS'];
	    } else {
		    $CACHE_MANAGER->StartTagCache($cacheDir);
		    $CACHE_MANAGER->RegisterTag($cacheId);

		    $result = self::getOption('notify_only_active_users');

		    $CACHE_MANAGER->EndTagCache();
		    if ($obCache->StartDataCache(36000, $cacheId, $cacheDir)) {
			    $obCache->EndDataCache(['ITEMS' => $result]);
		    }
	    }

	    return $result;
    }

    protected static function getGroups(): array
    {
        $result = [
            0 => Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_ACTIVE_USERS_GROUP_ID_CHOOSE')
        ];

        $rs = GroupTable::getList();
        while($arGroup = $rs->fetch()) {
            $result[$arGroup['ID']] = $arGroup['NAME'];
        }

        return $result;
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
