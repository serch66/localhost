<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Qsoft\Committees\Admin\Options;
use \Qsoft\Committees\Exception\CommitteesException;

Loc::loadMessages(__FILE__);

if(! Loader::includeModule('qsoft.committees')) {
    throw new CommitteesException(Loc::getMessage('QSOFT_COMMITTEES_OPTIONS_ERROR_MODULE_NOT_LOADED'), 400);
}

global $APPLICATION;

$APPLICATION->SetAdditionalCSS('/local/modules/qsoft.committees/assets/Sync.css');
Bitrix\Main\Page\Asset::getInstance()->addJs('/local/modules/qsoft.committees/assets/Sync.js');

CJSCore::Init(['jquery']);

$options = new Options();
$options->ensureOptionsSaveRequired();

$tabs = $options::getTabArray();

$tabControl = new CAdminTabControl(
    "tabControl",
    $tabs
);

$tabControl->Begin();
?>

    <form action="<?= ($APPLICATION->GetCurPage()) ?>?mid=<?= \Qsoft\Committees\MODULE_ID ?>&lang=<?= LANG ?>" method="post">
        <?
        foreach ($tabs as $aTab) {
            $tabControl->BeginNextTab();

            if ($aTab["BEFORE_HTML"]) {
                echo '<tr><td colspan="2">' . $aTab["BEFORE_HTML"] . '</td></tr>';
            }

            if ($aTab["OPTIONS"]) {
                __AdmSettingsDrawList(\Qsoft\Committees\MODULE_ID, $aTab["OPTIONS"]);
            } else if ($aTab['DIV'] === 'rights') {
                require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/admin/group_rights.php');
            }

            if ($aTab["AFTER_HTML"]) {
                echo '<tr><td colspan="2">' . $aTab["AFTER_HTML"] . '</td></tr>';
            }
        }

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="Сохранить" class="adm-btn-save" />
        <input type="submit" name="default" value="Вернуть стандартные" />

        <?= bitrix_sessid_post() ?>
    </form>
<?php $tabControl->End();
