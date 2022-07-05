<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Exception;
use Qsoft\Personal\Repository\UsersRepository;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class LoadUsers extends Step
{
    private const SYNC_STEP_SIZE = 50;
    private const SYNC_TRY_COUNT = 3;
    private const SYNC_TRY_DELAY = 3;

    protected $repUsers;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->repUsers = new UsersRepository();
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function process()
    {
        $percentage = $this->load();
        $this->setProgress($percentage);

        if ($percentage >= 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_LOAD_USERS_SUCCESS'));
            $this->setField('page', 0);
            $this->nextStep('LoadDirections');
        }
    }

    /**
     * @return bool
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @throws Exception
     */
    protected function load(): int
    {
        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $page = (int)$this->getField('page');

        $filter = [];
        if ($this->getManager()->getDateFrom()) {
            $filter['dtUpdateFrom'] = $this->getManager()->getDateFrom();
        }

        while (
            $users = $this->getItems(
                $filter,
                ['pageNum' => $page, 'pageSize' => self::SYNC_STEP_SIZE]
            )
        ) {
            $this->processItems($users);

            $page++;

            if ($page > 1) {
                if (
                    $maxInterval &&
                    time() - $time > $maxInterval
                ) {
                    break;
                }
            } else {
                break;
            }
        }

        $this->setField('page', $page);

        $count = $page * self::SYNC_STEP_SIZE;
        if ($count > $this->repUsers->getTotalCount())
            $count = $this->repUsers->getTotalCount();

        $message = Loc::getMessage('QSOFT_PERSONAL_SYNC_LOAD_USERS') . $count . '/' . $this->repUsers->getTotalCount();

        $this->setMessage($message);
        $this->getLogger()->info($message);

        if (!$this->repUsers->getPageCount()) {
            return 100;
        }

        return (int)($page * 100 / $this->repUsers->getPageCount());
    }

    protected function getItems(array $filter = [], array $nav = []): array
    {
        $tryCount = 0;

        while (++$tryCount <= self::SYNC_TRY_COUNT) {
            try {
                return $this->repUsers->getList($filter, $nav);
            } catch (\Exception $e) {
                $this->getLogger()->error($e->getMessage(), [$e->getTraceAsString()]);

                if ($tryCount >= self::SYNC_TRY_COUNT) {
                    throw $e;
                } else {
                    sleep(self::SYNC_TRY_DELAY);
                }
            }
        }

        return [];
    }

    protected function processItems(array $users)
    {
        $data = $this->getData('users');
        if (!is_array($data))
            $data = [];

        foreach ($users as $user) {
            if (empty($user['sid'])) {
                continue;
            }

            $data[$user['id']] = $user;
        }

        $this->setData('users', $data);
    }
}
