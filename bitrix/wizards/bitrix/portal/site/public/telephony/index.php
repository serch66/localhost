<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/telephony/index.php");

$APPLICATION->SetTitle(GetMessage("VI_PAGE_STAT_TITLE"));
?>

<?$APPLICATION->IncludeComponent("bitrix:ui.sidepanel.wrapper",
	"",
	array(
		"POPUP_COMPONENT_NAME" => "bitrix:voximplant.start",
		"POPUP_COMPONENT_TEMPLATE_NAME" => "",
		"USE_PADDING" => false
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>