<?php

namespace Qsoft\Personal\Admin;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UI\Extension;
use CJSCore;
use Exception;
use Qsoft\Personal\Sync\Manager;
use Qsoft\Personal\Sync\SyncStatus;
use TypeError;

use const Qsoft\Personal\BASE_DIR;
use const Qsoft\Personal\MODULE_ID;

Loc::loadLanguageFile(__FILE__);

class Options
{
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

                        Option::set(MODULE_ID,
                            $option[0],
                            is_array($optionValue) ? implode(",", $optionValue) : $optionValue
                        );
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
        $defaultVal = Option::getDefaults(MODULE_ID);

        $hours = [];
        for ($h = 0; $h < 24; $h++) {
            $hours[$h] = $h . ':00';
        }

        Option::set(
                MODULE_ID,
                'sync_from',
                self::getLastSyncDate() ? self::getLastSyncDate()->toString() : ''
        );

        return [
            [
                "DIV" => "edit",
                "TAB" => Loc::getMessage("QSOFT_TAB_EDIT"),
                "TITLE" => Loc::getMessage("QSOFT_TAB_EDIT_TITLE"),
                "OPTIONS" => [
                    [
                        "active",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_ACTIVE"),
                        $defaultVal['active'] ?: "",
                        ["checkbox"]
                    ],
                    [
                        "protocol",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_PROTOCOL"),
                        $defaultVal['protocol'] ?: "",
                        [
                            "selectbox",
                            [
                                "http" => 'http',
                                "https" => 'https',
                            ]
                        ]
                    ],
                    [
                        "host",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_HOST"),
                        $defaultVal['host'] ?: "",
                        ["text", 100]
                    ],
                    [
                        "key",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_KEY"),
                        $defaultVal['key'] ?: "",
                        ["text", 100]
                    ],
                    [
                        "default_direction_id",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_DEFAULT_DIRECTION_ID"),
                        $defaultVal['default_direction_id'] ?: "",
                        ["text"]
                    ],
                    [
                        "default_direction_name",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_DEFAULT_DIRECTION_NAME"),
                        $defaultVal['default_direction_name'] ?: "",
                        ["text", 100]
                    ],
                    Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SCHEDULE"),
                    [
                        "schedule_full",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SCHEDULE_FULL"),
                        $defaultVal['schedule_full'] ?: "",
                        [
                            "multiselectbox",
                            $hours
                        ]
                    ],
                    [
                        "schedule_changed",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SCHEDULE_CHANGES"),
                        $defaultVal['schedule_changed'] ?: "",
                        [
                            "multiselectbox",
                            $hours
                        ]
                    ]
                ],
            ],
            [
                "DIV" => "urls",
                "TAB" => Loc::getMessage("QSOFT_PERSONAL_TAB_URLS"),
                "TITLE" => Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_TITLE"),
                "OPTIONS" => [
                    Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_USERS"),
                    [
                        "method_users_path",
                        Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_PATH"),
                        $defaultVal['method_users_path'] ?: "",
                        ["text", 100],
                    ],
                    [
                        "method_users_type",
                        Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY"),
                        $defaultVal['method_users_type'] ?: "",
                        [
                            "selectbox",
                            [
                                "GET" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_GET"),
                                "POST" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_POST"),
                                "DELETE" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_DELETE"),
                            ]
                        ]
                    ],
                    Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_PHOTOS"),
                    [
                        "method_photos_path",
                        Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_PATH"),
                        $defaultVal['method_photos_path'] ?: "",
                        ["text", 100],
                    ],
                    [
                        "method_photos_type",
                        Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY"),
                        $defaultVal['method_photos_type'] ?: "",
                        [
                            "selectbox",
                            [
                                "GET" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_GET"),
                                "POST" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_POST"),
                                "DELETE" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_DELETE"),
                            ]
                        ]
                    ],
                    Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_DIRECTIONS"),
                    [
                        "method_directions_path",
                        Loc::getMessage("QSOFT_PERSONAL_TAB_URLS_PATH"),
                        $defaultVal['method_directions_path'] ?: "",
                        ["text", 100],
                    ],
                    [
                        "method_directions_type",
                        Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY"),
                        $defaultVal['method_directions_type'] ?: "",
                        [
                            "selectbox",
                            [
                                "GET" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_GET"),
                                "POST" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_POST"),
                                "DELETE" => Loc::getMessage("QSOFT_PERSONAL_TYPE_QUERY_DELETE"),
                            ]
                        ]
                    ],
                ],
            ],
            [
                "DIV" => "sync",
                "TAB" => Loc::getMessage("QSOFT_PERSONAL_OPTIONS_TAB_SYNC"),
                "TITLE" => Loc::getMessage("QSOFT_PERSONAL_OPTIONS_TITLE_SYNC"),
                "OPTIONS" => [
                    [
                        "sync_type",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_TYPE"),
                        $defaultVal['sync_type'] ?: "",
                        [
                            "selectbox",
                            [
                                "full" => Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_TYPE_FULL"),
                                "changes" => Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_TYPE_CHANGES"),
                            ]
                        ]
                    ],
                    [
                        "sync_from",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_FROM"),
                        '',
                        ["text"]
                    ],
                    [
                        "last_sync_format",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_LAST_SYNC"),
                        self::getLastSyncDate() ?
                            self::getLastSyncDate()->toString() :
                            Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_FROM_EMPTY"),
                        ["statictext"]
                    ],
                    [
                        "step_max_time",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_STEP_MAX_TIME"),
                        $defaultVal['step_max_time'] ?: "15",
                        ["text"]
                    ],
                    [
                        "step_delay_time",
                        Loc::getMessage("QSOFT_PERSONAL_OPTIONS_SYNC_STEP_DELAY_TIME"),
                        $defaultVal['step_delay_time'] ?: "0",
                        ["text"]
                    ],
                ],
                "AFTER_HTML" => self::getSyncBlock()
            ],
            [
                'DIV'   => 'rights',
                'TAB'   => Loc::getMessage('MAIN_TAB_RIGHTS'),
                'TITLE' => GetMessage('MAIN_TAB_TITLE_RIGHTS')
            ]
        ];
    }

    /**
     * Метод возвращает активность модуля
     *
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function isActive(): bool
    {
        return Option::get(MODULE_ID, 'active', 'N') === 'Y';
    }

    /**
     * Метод возвращает протокол соединения
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getProtocol(): string
    {
        return Option::get(MODULE_ID, 'protocol', '');
    }

    /**
     * Метод возвращает хост для соединения
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getHost(): string
    {
        return Option::get(MODULE_ID, 'host', '');
    }

    /**
     * Метод для получения URL
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getUrl(): string
    {
        $url = '';

        if (!empty($protocol = self::getProtocol()) && !empty($host = self::getHost())) {
            $url = $protocol . '://' . $host;
        }

        return $url;
    }

    /**
     * Метод для получения токена
     *
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getApiKey(): string
    {
        return Option::get(MODULE_ID, 'key', '');
    }

    /**
     * Метод возвращает настройки для конкретного действия
     *
     * @param string $urlOptionName
     * @param string $requestTypeOptionName
     * @param string $defaultMethod
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getInfo(string $urlOptionName, string $requestTypeOptionName, string $defaultMethod = ""): array
    {
        return [
            'url' => Option::get(MODULE_ID, $urlOptionName, ''),
            'method' => Option::get(MODULE_ID, $requestTypeOptionName, $defaultMethod),
        ];
    }

    /**
     * Возвращает дату последнего обновления заявок или null, если она не задана.
     *
     * @return DateTime|null
     */
    public static function getLastSyncDate(): ?DateTime
    {
        try {
            $date = (int)Option::get(MODULE_ID, "last_sync", 0);
            return $date ? DateTime::createFromTimestamp($date) : null;
        } catch(Exception|TypeError $e) {
            return null;
        }
    }

    /**
     * Сохраняет дату последнего обновления заявок.
     *
     * @param DateTime $date
     * @return bool
     */
    public static function setLastSyncDate(DateTime $date): bool
    {
        try {
            Option::set(
                MODULE_ID,
                "last_sync",
                $date->getTimestamp()
            );
            return true;
        } catch(Exception|TypeError $e) {
            return false;
        }
    }

    public static function getHoursForFullSync(): array
    {
        $schedule = Option::get(MODULE_ID, 'schedule_full', '');
        return explode(',', $schedule);
    }

    public static function getHoursForSync(): array
    {
        $schedule = Option::get(MODULE_ID, 'schedule_changed', '');
        return explode(',', $schedule);
    }

    public static function canSyncFull(): bool
    {
        $h = (int)date('H');
        $hours = Options::getHoursForFullSync();

        return in_array($h, $hours);
    }

    public static function canSyncChanged(): bool
    {
        $h = (int)date('H');
        $hours = Options::getHoursForSync();

        return in_array($h, $hours);
    }

    public static function getDefaultDirectionId(): int
    {
        return (int)Option::get(MODULE_ID, 'default_direction_id', 0);
    }

    public static function getDefaultDirectionName(): string
    {
        return Option::get(
            MODULE_ID,
            'default_direction_name',
            Loc::getMessage('QSOFT_PERSONAL_OPTIONS_DEFAULT_DIRECTION_NAME_VALUE')
        );
    }

    /**
     * @return string
     */
    protected static function getSyncBlock(): string
    {
        if (!Manager::isRun() && !Manager::isRun(null, true)) {
            CJSCore::Init(['qsoft_personal_sync', 'ui']);
            Extension::load('ui.progressbar');
            Extension::load("ui.alerts");
            Extension::load("ui.buttons");
            Extension::load("ui.buttons.icons");

            ob_start();
            include BASE_DIR . '/view/sync.php';
            return ob_get_clean();
        } else {
            $status = SyncStatus::getLast();
            return BeginNote('style="text-align:center;"') .
                Loc::getMessage(
                    "QSOFT_PERSONAL_OPTIONS_SYNC_IS_RUN",
                    ['MESSAGE' => $status->getMessage() . ' ' . $status->getProgress() . '%']
                ) . EndNote();
        }
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
