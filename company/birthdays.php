<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/company/birthdays.php");
$APPLICATION->SetTitle(GetMessage("COMPANY_TITLE"));
?><?$APPLICATION->IncludeComponent(
	"efko:intranet.structure.birthday.nearest",
	"s.a.truhachev",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"DATE_FORMAT" => "d.m.Y",
		"DATE_FORMAT_NO_YEAR" => "d.m",
		"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
		"DEPARTMENT" => "0",
		"DEPARTMENT_REMUVE" => array("0","20","21","22"),
		"NAME_TEMPLATE" => "",
		"NUM_USERS" => "25",
		"PATH_TO_CONPANY_DEPARTMENT" => "/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT=#ID#",
		"PM_URL" => "/company/personal/messages/chat/#USER_ID#/",
		"SHOW_FILTER" => "Y",
		"SHOW_LOGIN" => "Y",
		"SHOW_YEAR" => "Y",
		"STRUCTURE_FILTER" => "structure",
		"STRUCTURE_PAGE" => "structure.php",
		"USER_PROPERTY" => array("PERSONAL_PHOTO","PERSONAL_PHONE","PERSONAL_MOBILE","WORK_PHONE","UF_DEPARTMENT")
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>