<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Qsoft\Personal\Repository\DirectionsRepository;

Loc::loadMessages(__FILE__);

class Init extends Step
{
    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_INIT'));

        $fromDate = $this->getManager()->getDateFrom();

        if ($fromDate instanceof DateTime) {
            $this->getLogger()->info(Loc::getMessage(
                "QSOFT_PERSONAL_SYNC_START_FROM_DATE",
                ["#date#" => $fromDate ? $fromDate->toString() : '']
            ));
        } else {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_START'));
        }

        $this->loadBitrixUsers();
        $this->loadBitrixDirections();

        $this->nextStep('LoadUsers');
    }

    protected function loadBitrixUsers(): void
    {
        if ($this->getManager()->syncChanges()) {
            $this->setData('bitrix_users', []);
            return;
        }

        $rs = UserTable::getList(
            [
                'filter' => [
                    '!UF_SID' => false,
                    'ACTIVE' => true
                ],
                'select' => ['ID', 'UF_SID']
            ]
        );

        $users = array_column($rs->fetchAll(), 'ID', 'UF_SID');
        $this->setData('bitrix_users', $users);
    }

    protected function loadBitrixDirections(): void
    {
        if ($this->getManager()->syncChanges()) {
            $this->setData('bitrix_directions', []);
            return;
        }

        $directions = array_column(DirectionsRepository::getAllSections(), 'ID', 'ID');
        $this->setData('bitrix_directions', $directions);


    }
}
