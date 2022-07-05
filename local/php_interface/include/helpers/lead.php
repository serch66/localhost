<?php

namespace CrmCustom;

use Bitrix\Main\Loader;

Loader::includeModule('crm');

class Lead
{
    static public function FindCompany($Inn){

        if($Inn){
            $res = \Bitrix\Crm\RequisiteTable::GetList([
                'select' => ['ENTITY_ID', 'ID', 'ASSIGNED' => 'Company.ASSIGNED_BY_ID', /*'DIVISION' => 'Company.UF_CRM_1638526416'*/],
                'filter' => [
                    'RQ_INN' => $Inn,
                    'ENTITY_TYPE_ID' => \CCrmOwnerType::Company,
                    //'Company.UF_CRM_1638526416' => 9385
                ],
                'runtime'   => [
                    new \Bitrix\Main\Entity\ReferenceField(
                        'Company',
                        '\Bitrix\Crm\CompanyTable',
                        array(
                            '=this.ENTITY_ID' => 'ref.ID'
                        ),
                        array('join_type' => 'INNER')
                    ),
    
                ]
            ])->Fetch();
            $assignedId = (int)$res['ASSIGNED'];

            if( $assignedId > 0 )
                return  "user_{$assignedId}";
        }
        return 0;
    }

}