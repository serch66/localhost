<?php

namespace CrmCustom;

use Bitrix\Main\Loader;

Loader::includeModule('crm');
Loader::includeModule('im');
Loader::includeModule('bizproc');


class ImopenlineMessage
{
    static public function OnImopenlineMessageReceive(\Bitrix\Main\Event $event)
    {
        define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/chat.txt");
        $Message = $event->getParameter('MESSAGE');
        $ChatId = $event->getParameter('TO_CHAT_ID');
        
        $pattern = '/(\d{10})/';
        
        preg_match($pattern, $Message, $find);
        
        
       
        if(count($find) > 0){
            $INN = $find[0];
            $LeadId = self::GetLead($ChatId, $INN);
            
        }
        
    }
    static function GetLead($id, $inn){
        $FieldInn = 'UF_CRM_INN'; 
        $filter = [
          //'ENTITY_ID' => 'telegrambot%',
           '=ID' => $id
        ];
        
        $res = \Bitrix\Im\Model\ChatTable::GetList(['filter' => $filter])->Fetch();
        \AddMessage2Log(print_r($res, true), 'res');
        
        if($res){
            $Data = explode('|', $res['ENTITY_DATA_1']);
            
            $LeadId = (int)$Data[2];
            $arLead = \Bitrix\Crm\LeadTable::GetList(['filter' => ['=ID' => $LeadId], 'select' => ['ID', $FieldInn]])->Fetch();
            
            \AddMessage2Log('Lead: '.$LeadId, 'Lead');
            \AddMessage2Log('Inn: '.$inn, 'Inn');
            \AddMessage2Log(print_r($arLead, true), 'res');
            
         
            
            if($arLead[$FieldInn] != $inn){
                \AddMessage2Log('Update lead', 'Update lead');
                
                \Bitrix\Crm\LeadTable::Update($LeadId, [$FieldInn => $inn]);
                
                $workflowTemplateId = 329;
                
                \AddMessage2Log('Update lead end', 'Update lead end');
                
                $wfId = \CBPDocument::StartWorkflow(
                       $workflowTemplateId,
                       array("crm", "CCrmDocumentLead", 'LEAD_'.$LeadId),
                       [],
                       $arErrorsTmp
                );
                
                \AddMessage2Log(\print_r($arErrorsTmp, true), 'BP');
                \AddMessage2Log('wfId: '.$wfId, 'BP');
                
            }
            
        }
        
        return ;
        
    }
    
}