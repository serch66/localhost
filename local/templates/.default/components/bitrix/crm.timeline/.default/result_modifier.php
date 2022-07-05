<?php

$skip_stages = ['2', 'UC_41974C'];
$skip_stattus_id = [ 3, 4, 5, 6];
$skipAll = [3, 4];

$Type = \Bitrix\Crm\Activity\Provider\Call::getTypes()[0];
$DirectionName = $Type['DIRECTIONS'];

$HISTORY_ITEMS = [];
\Bitrix\Main\Loader::includeModule('crm');


foreach ($arResult['HISTORY_ITEMS'] as &$HistoryItem) {
    
    $DirectionId = $HistoryItem['ASSOCIATED_ENTITY']['DIRECTION'] ?? intval($HistoryItem['TYPE_ID']);
    $entety_id = $HistoryItem['ASSOCIATED_ENTITY']['OWNER_ID'] ?? $HistoryItem['ASSOCIATED_ENTITY_ID'];
    $entety_type = $HistoryItem['ASSOCIATED_ENTITY']['OWNER_TYPE_ID'] ?? $HistoryItem['ASSOCIATED_ENTITY_TYPE_ID'];
    
    
    if (in_array($DirectionId, $skip_stattus_id) && intval($entety_type) === \CCrmOwnerType::Lead) {
        
        
        $HistoryItem['ASSOCIATED_ENTITY']['ITEM_IDS'] = [];
        $HistoryItem['ASSOCIATED_ENTITY']['CALL_INFO']['CALL_TYPE_TEXT'] = $DirectionName[$DirectionId];
        $lead = \CCrmLead::GetListEx(['ID' => 'DESC'], ['=ID' => intval($entety_id), 'CHECK_PERMISSIONS' => 'N'], false,
            false, ['ID', 'STATUS_ID'])->Fetch();
            $stageid = $lead['STATUS_ID'];
            
            if (in_array($DirectionId, $skipAll)) {
                $arResult['STAGE_ID_STATUS'] = 'skip';
                unset($HistoryItem);
                continue;
            }
            
            if (in_array($stageid, $skip_stages)) {
                unset($HistoryItem);
                $arResult['STAGE_ID_STATUS'] = 'skip';
                continue;
            }
            
    }
    $HISTORY_ITEMS[] = $HistoryItem;
}
$arResult['HISTORY_ITEMS'] = $HISTORY_ITEMS;
