<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Exception;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class DeactivateDirections extends Step
{
    protected const SYNC_STEP_SIZE = 50;

    protected $bitrix_directions = [];

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->bitrix_directions = $this->getData('bitrix_directions');

        if ((int)$this->getField('all_count') === 0) {
            $this->setField('all_count', count($this->bitrix_directions));
        }
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_DIRECTIONS'));

        $percentage = $this->save();
        $this->setProgress($percentage);

        if ($percentage >= 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_DIRECTIONS_SUCCESS'));
            $this->setField('all_count', 0);
            $this->nextStep('SaveUsers');
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
        if (empty($this->bitrix_directions))
            return 100;

        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $cnt = 0;

        while (
            $directions = $this->getItems()
        ) {
            $this->processItems($directions);
            $cnt += count($directions);

            if (
                $maxInterval &&
                time() - $time > $maxInterval
            ) {
                break;
            }
        }

        $this->setData('bitrix_directions', $this->bitrix_directions);

        $allCount = $this->getField('all_count');
        $currentCount =  count($this->bitrix_directions);

        $this->setMessage(
            Loc::getMessage('QSOFT_PERSONAL_SYNC_DEACTIVATE_DIRECTIONS') .
            ($allCount - $currentCount) . '/' . $allCount
        );

        return (int)(100 - $currentCount * 100 / $allCount);
    }

    protected function getItems(): array
    {
        $cnt = 0;
        $items = [];

        while (
            $cnt++ < self::SYNC_STEP_SIZE &&
            !empty($this->bitrix_directions)
        ) {
            $items[] = array_pop($this->bitrix_directions);
        }

        return $items;
    }

    protected function processItems(array $items)
    {
        if (empty($items)) {
            return;
        }

        $rs = new \CIBlockSection();

        foreach ($items as $id) {
            $rs->Update($id, ['ACTIVE' => 'N']);
        }
    }
}
