<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Exception;
use Qsoft\Personal\Repository\UsersRepository;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class DeactivateUsers extends Step
{
    protected const SYNC_STEP_SIZE = 50;

    protected $bitrix_users = [];

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->bitrix_users = $this->getData('bitrix_users');

        if ((int)$this->getField('all_count') === 0) {
            $this->setField('all_count', count($this->bitrix_users));
        }
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_USERS'));

        $percentage = $this->save();
        $this->setProgress($percentage);

        if ($percentage >= 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_USERS_SUCCESS'));
            $this->setField('all_count', 0);
            $this->nextStep('SaveHeads');
        }
    }

    /**
     * @return bool
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @throws Exception
     */
    protected function save(): int
    {
        if (empty($this->bitrix_users))
            return 100;

        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $cnt = 0;

        while (
            $users = $this->getItems()
        ) {
            $this->processItems($users);
            $cnt += count($users);

            if (
                $maxInterval &&
                time() - $time > $maxInterval
            ) {
                break;
            }
        }

        $this->setData('bitrix_users', $this->bitrix_users);

        $allCount = $this->getField('all_count');
        $currentCount =  count($this->bitrix_users);

        $this->setMessage(
            Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_USERS') .
            ($allCount - $currentCount) . '/' . $allCount
        );

        return (int)(100 - $currentCount * 100 / $allCount);
    }

    protected function getItems(): array
    {
        $cnt = 0;
        $users = [];

        while (
            $cnt++ < self::SYNC_STEP_SIZE &&
            !empty($this->bitrix_users)
        ) {
            $users[] = array_pop($this->bitrix_users);
        }

        return $users;
    }

    protected function processItems(array $users)
    {
        if (empty($users)) {
            return;
        }

        $rs = new \CUser();

        foreach ($users as $id) {
            $rs->Update($id, ['ACTIVE' => 'N']);
        }
    }
}
