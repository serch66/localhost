<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Finish extends Step
{
    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_FINISH'));
        $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_FINISH'));
        $this->setFinished(true);
        $this->setProgress(100);
    }
}
