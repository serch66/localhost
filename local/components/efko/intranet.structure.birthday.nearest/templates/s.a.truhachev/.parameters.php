<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$IBLOCK_ID = COption::GetOptionInt('intranet', 'iblock_structure', false);

$arSections = array(0 => '');
if ($IBLOCK_ID !== false)
{
    $dbRes = CIBlockSection::GetTreeList(array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y'));
    while ($arRes = $dbRes->Fetch())
    {
        $arSections[$arRes['ID']] = trim(str_repeat('. ', $arRes['DEPTH_LEVEL']-1).' '.$arRes['NAME']);
    }
}

$arTemplateParameters = array(
    "DEPARTMENT_REMUVE" => array(
        "NAME" => GetMessage('INTR_PREDEF_DEPARTMENT_REMUVE'),
        "TYPE" => "LIST",
        'VALUES' => $arSections,
        "MULTIPLE"  =>  "Y",
        "DEFAULT" => '',
        "SIZE" => '10',
    ),
	"SHOW_FILTER" => array(
		"NAME" => GetMessage('INTR_ISBN_TPL_PARAM_SHOW_FILTER'),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => 'Y',
	),
); 
?>