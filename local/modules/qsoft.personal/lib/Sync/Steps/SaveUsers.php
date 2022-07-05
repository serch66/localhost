<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Exception;
use Qsoft\Personal\Repository\UsersRepository;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class SaveUsers extends Step
{
    protected const SYNC_STEP_SIZE = 20;

    protected $repUsers;
    protected $users = [];
    protected $bitrix_users = [];

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->repUsers = new UsersRepository();
        $this->users = $this->getData('users');
        $this->bitrix_users = $this->getData('bitrix_users');

        if ((int)$this->getField('all_count') === 0) {
            $this->setField('all_count', count($this->users));
        }
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_USERS'));

        $percentage = $this->save();
        $this->setProgress($percentage);

        if ($percentage >= 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_USERS_SUCCESS'));
            $this->setField('all_count', 0);
            $this->nextStep('DeactivateUsers');
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
        if (empty($this->users))
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

        $this->setData('users', $this->users);
        $this->setData('bitrix_users', $this->bitrix_users);

        $allCount = $this->getField('all_count');
        $currentCount =  count($this->users);

        $this->setMessage(
            Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_USERS') .
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
            !empty($this->users)
        ) {
            $users[] = array_pop($this->users);
        }

        return $users;
    }

    protected function processItems(array $users)
    {
        if (empty($users)) {
            return;
        }

        foreach ($users as $user) {
            if (!$user['sid']) continue;

            try {
                $this->repUsers->updateBySid($user['sid'], $user);
            } catch (\Exception $e) {
                $this->getManager()->getLogger()->error($e->getMessage() . ': ' . $e->getTraceAsString(), $user);
            }
            unset($this->bitrix_users[$user['sid']]);
        }
    }
}
