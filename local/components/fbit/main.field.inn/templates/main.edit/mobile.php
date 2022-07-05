<?php

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var IntegerUfComponent $component
 * @var array $arResult
 */
$component = $this->getComponent();

$productEditorCfg = [];
$productEditorCfg['requisiteAjaxUrl'] = '/bitrix/components/bitrix/crm.requisite.edit/innerform.ajax.php?&site=' . SITE_ID . '&' . bitrix_sessid_get();

?>
<span class='fields integer field-wrap' id="CLIENT">
    <? foreach($arResult['value'] as $value): ?>
        <? $productEditorCfg['attrList'] = $value['attrList']?>
        <span class='fields integer field-item'>
	    <input size="20" class="fields integer " name="INN_FIELD" id="INN_FIELD" type="text" tabindex="0" value="" autocomplete="off">
    </span>
    <? endforeach; ?>
<script type="text/javascript">
    BX.ready(
        function(){
            var editor = BX.Crm.CreateCompany.create(<?=CUtil::PhpToJSObject($productEditorCfg)?>);
        });
</script>
</span>