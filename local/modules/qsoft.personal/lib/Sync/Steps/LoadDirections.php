<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main\Localization\Loc;
use Qsoft\Personal\Repository\DirectionsRepository;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class LoadDirections extends Step
{
    private const SYNC_STEP_SIZE = 50;
    private const SYNC_TRY_COUNT = 3;
    private const SYNC_TRY_DELAY = 3;

    protected $repDirections = null;
    protected $directions = [];

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->repDirections = new DirectionsRepository();
        $this->directions = $this->getData('directions');
        if (!is_array($this->directions))
            $this->directions = [];
    }

    public function process()
    {
        $percentage = $this->load();
        $this->setProgress($percentage);

        if ($percentage >= 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_LOAD_DIRECTIONS_SUCCESS'));
            $this->setField('page', 0);
            $this->nextStep('SaveDirections');
        }
    }

    protected function load(): int
    {
        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $page = $this->getField('page');

        $filter = [];
        if ($this->getManager()->getDateFrom()) {
            $filter['dtUpdateFrom'] = $this->getManager()->getDateFrom();
        }

        while (
        $directions = $this->getItems(
            $filter,
            ['pageNum' => $page, 'pageSize' => self::SYNC_STEP_SIZE]
        )
        ) {
            $this->processItems($directions);

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
        $this->setData('directions', $this->directions);

        $count = $page * self::SYNC_STEP_SIZE;
        if ($count > $this->repDirections->getTotalCount())
            $count = $this->repDirections->getTotalCount();

        $message = Loc::getMessage('QSOFT_PERSONAL_SYNC_LOAD_DIRECTIONS') . $count . '/' . $this->repDirections->getTotalCount();

        $this->setMessage($message);
        $this->getLogger()->info($message);

        if (!$this->repDirections->getPageCount()) {
            return 100;
        }

        return intval($page * 100 / $this->repDirections->getPageCount());
    }

    protected function getItems(array $filter = [], array $nav = []): array
    {
        $tryCount = 0;

        while (++$tryCount <= self::SYNC_TRY_COUNT) {
            try {
                return $this->repDirections->getList($filter, $nav);
            } catch (\Exception $e) {
                if ($tryCount > self::SYNC_TRY_COUNT) {
                    throw $e;
                } else {
                    sleep(self::SYNC_TRY_DELAY);
                }
            }
        }

        return [];
    }

    protected function processItems(array $items)
    {
        foreach ($items as $item) {
            $this->directions[$item['id']] = $item;
        }
    }
}
