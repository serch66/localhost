<?php

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Qsoft\Personal\Admin\Options;

use const Qsoft\Personal\MODULE_ID;

Loc::loadMessages(__FILE__);

try {
    Loader::includeModule('qsoft.personal');
} catch (LoaderException $e) {
    ShowError($e);
}

global $APPLICATION;

$APPLICATION->SetAdditionalCSS('/local/modules/qsoft.personal/assets/Sync.css');
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/modules/qsoft.personal/assets/Sync.js');

CJSCore::Init(['jquery']);

$options = new Options();
$options->ensureOptionsSaveRequired();

$tabs = $options::getTabArray();

$tabControl = new CAdminTabControl(
    "tabControl",
    $tabs
);

$tabControl->Begin(); ?>
    <form action="<?= ($APPLICATION->GetCurPage()) ?>?mid=<?=MODULE_ID ?>&lang=<?= LANG ?>" method="post">
        <?
        foreach ($tabs as $aTab) {
            $tabControl->BeginNextTab();

            if ($aTab["BEFORE_HTML"]) {
                echo '<tr><td colspan="2">' . $aTab["BEFORE_HTML"] . '</td></tr>';
            }

            if ($aTab["OPTIONS"]) {
                __AdmSettingsDrawList(MODULE_ID, $aTab["OPTIONS"]);
            } else if ($aTab['DIV'] === 'rights') {
                require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/admin/group_rights.php');
            }

            if ($aTab["AFTER_HTML"]) {
                echo '<tr><td colspan="2">' . $aTab["AFTER_HTML"] . '</td></tr>';
            }
        }

        $tabControl->Buttons();
        ?>

        <input type="submit" <?if (!Options::canWrite()) echo 'disabled' ?> name="apply" value="Сохранить" class="adm-btn-save" />
        <input type="submit" <?if (!Options::canWrite()) echo 'disabled' ?> name="default" value="Вернуть стандартные" />

        <?= bitrix_sessid_post() ?>
    </form>
<? $tabControl->End();

