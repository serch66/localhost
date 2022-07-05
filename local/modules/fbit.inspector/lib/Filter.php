<?php

namespace Fbit\Inspector;

use Bitrix\Main\Config\Option;

class Filter
{
    protected $DepartmentId = 0;

    protected $LeftMargin = null;
    protected $RightMargin = null;
    protected $IblockId = null;

    public function __construct()
    {
        $this->DepartmentId = Option::get(MODULE_ID, 'department_id', 0);
        $Department = \Bitrix\Iblock\SectionTable::GetList(['filter' => ['=ID' => $this->DepartmentId]])->Fetch();

        if ($Department) {
            $this->LeftMargin = $Department['LEFT_MARGIN'];
            $this->RightMargin = $Department['RIGHT_MARGIN'];
            $this->IblockId = $Department['IBLOCK_ID'];
        }
    }

    public function GetLeft()
    {
        return $this->LeftMargin;
    }

    public function GetRight()
    {
        return $this->RightMargin;
    }

    public function GetFilter(&$Filter)
    {
        if ($this->LeftMargin > 0 && $this->RightMargin > 0) {
            $Filter['!><LEFT_BORDER'] = [$this->LeftMargin, $this->RightMargin];
        }
    }

    public function CheckCurrent(&$Id)
    {
        $Department = \Bitrix\Iblock\SectionTable::GetList(
            [
                'select' => ['ID'],
                'filter' => ['=ID' => $Id, '>=LEFT_MARGIN' => $this->LeftMargin, '<=RIGHT_MARGIN' => $this->RightMargin]
            ]
        )->Fetch();

        if ($Department) {
            LocalRedirect('/company/structure.php');
            return -1;
        }
        return $Id;
    }

    public function PrepareDepartment(&$User)
    {
        $rsDepartment = \Bitrix\Iblock\SectionTable::GetList(
            [
                'select' => ['ID'],
                'filter' => [
                    //'=ID' => $User['UF_DEPARTMENT'],
                    'IBLOCK_ID'            => $this->IblockId,
                    '>=LEFT_MARGIN'     => $this->LeftMargin,
                    '<=RIGHT_MARGIN'     => $this->RightMargin
                ]
            ]
        );

        $unSetDepartment = [];
        while ($ar = $rsDepartment->Fetch()) {

            $id = $ar['ID'];

            $User['DELETE_DEPARTMENT'][] = $id;


            $key = array_search($id, $User['UF_DEPARTMENT']);
            if($key !== false)
                unset($User['UF_DEPARTMENT'][$key]);
        }
        $User['UF_DEPARTMENT'] = array_values($User['UF_DEPARTMENT']);
    }

    public function PrepareStructureDepartment(&$Items)
    {
        $Ids = array_unique(array_column($Items, 'entityId'));

        $rsDepartment = \Bitrix\Iblock\SectionTable::GetList(
            [
                'select' => ['ID'],
                'filter' => [
                    '=ID' => $Ids,
                    '>=LEFT_MARGIN' => $this->LeftMargin,
                    '<=RIGHT_MARGIN' => $this->RightMargin
                ]
            ]
        );

        while ($ar = $rsDepartment->Fetch()) {
            $id = $ar['ID'];

            unset($Items['DR' . $id]);
            unset($Items['D' . $id]);
        }
    }
    function GetDeleteDepatments(){
        $rs = \Bitrix\Iblock\SectionTable::GetList(['filter' => ['>=LEFT_MARGIN' => $this->LeftMargin, '<=RIGHT_MARGIN' => $this->RightMargin]]);
        while($ar = $rs->Fetch()){
            $this->DeleteDepatments[] = $ar['ID'];
        }

    }
    function DeleteDepartment(&$DepartmentList){
        if($this->DeleteDepatments === null){
            $this->GetDeleteDepatments();
        }

        $result = array_diff($DepartmentList, $this->DeleteDepatments);

        return $result;
    }

    function SatructureDepartmant(&$DepartmentList){
        if($this->DeleteDepatments === null){
            $this->GetDeleteDepatments();
        }


        foreach($this->DeleteDepatments as $IdDepart){
            unset($DepartmentList['DR'.$IdDepart]);


        }

    }

}
