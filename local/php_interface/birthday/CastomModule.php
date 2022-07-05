<?php



require_once dirname(__DIR__) . '/../vendor/autoload.php';
require_once __DIR__ . '/../custom/autoload.php';


class CastomModule
{
    protected int $user;
    protected string $department;



    public function __construct()
    {
        $this->department = \Bitrix\Main\UserUtils::getDepartmentNames([$this->user]);

        return $this->setDepartmentThisUser();
    }




    public function getDepartmentThisUser()
    {

        $rsUsers = CUser::GetList();

    }

    /**
     * @return string
     */
    public function getDepartment($department): string
    {
        $this->department = $department;
        return $this->department;
    }








    private function setDepartmentThisUser()
    {
        //получаем текущего пользователя
        $this->user = CUser::GetID();

        $this->department = \Bitrix\Main\UserUtils::getDepartmentNames([$this->user]);

        $filter = Array
        (
            "UF_DEPARTMENT" => $this->department,
        );
        $rsUsers = CUser::GetList(($order="desc"), $filter); // выбираем пользователей

        $is_filtered = $rsUsers->is_filtered; // отфильтрована ли выборка ?


        return $rsUsers;
    }



}