<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Fbit\Inspector\Admin\Options;
use const Fbit\Inspector\MODULE_ID;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('fbit.inspector')) {
    ShowError('Модуль не установлен');
}

CJSCore::Init(['jquery']);

$options = new \Fbit\Inspector\Admin\Options();

if (!Options::canRead()) {
    return;
}

$options->ensureOptionsSaveRequired();

$tabs = $options::getTabArray();

$tabControl = new CAdminTabControl(
    "tabControl",
    $tabs
);

$tabControl->Begin();
?>
<form action="<?=($APPLICATION->GetCurPage())?>?mid=<?=MODULE_ID?>&lang=<?=LANG?>" method="post">

    <?
    foreach($tabs as $aTab) {
        $tabControl->BeginNextTab();
        if ($aTab["OPTIONS"]) {
            __AdmSettingsDrawList(MODULE_ID, $aTab['OPTIONS']);
        } else if ($aTab['DIV'] === 'rights') {
            require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/admin/group_rights.php');
        }
    }
    $tabControl->Buttons();
    ?>

    <input type="submit" <?if (!Options::canWrite()) echo 'disabled' ?> name="apply" value="Сохранить" class="adm-btn-save" />
    <input type="submit" <?if (!Options::canWrite()) echo 'disabled' ?> name="default" value="Вернуть стандартные" />

    <?=bitrix_sessid_post()?>
</form>
<?
$tabControl->End();

