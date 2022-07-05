<?php

namespace CrmCustom;

use Bitrix\Main\Loader;

Loader::includeModule('crm');

class CrmActivityCall
{
    static $direction = null; 
   
    static function SetNewDirection($EntityId, $Directions)
    {
      
        $filter = ['PROVIDER_TYPE_ID' => 'CALL', 'OWNER_TYPE_ID' => \CCrmOwnerType::Lead, 'OWNER_ID' => $EntityId, 'DIRECTION' => array_keys($Directions)]; 
        
        $select = ['ID', 'DIRECTION'];
        $res = \Bitrix\Crm\ActivityTable::GetList(['filter' => $filter, 'select' => $select]);
        
        
        while($ar = $res->Fetch()){
            
            if(isset($Directions[$ar['DIRECTION']])){
                $NewDirection = ['DIRECTION' => $Directions[$ar['DIRECTION']]];
                $r = \Bitrix\Crm\ActivityTable::Update($ar['ID'], $NewDirection);
                $result[] = 'Обновление направления звонка '.$ar['ID'];
                
            }
           
        }
        return \implode(',', $result);
    }

    
}