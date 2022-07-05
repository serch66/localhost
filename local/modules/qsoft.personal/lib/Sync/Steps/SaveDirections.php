<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Qsoft\Personal\Repository\DirectionsRepository;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class SaveDirections extends Step
{
    protected $repDirections = null;
    protected $directions = [];
    protected $bitrix_directions = [];
    protected $sections = [];

    public function __construct(Manager $manager)
    {
        Loader::includeModule('iblock');

        parent::__construct($manager);

        $this->repDirections = new DirectionsRepository();
        $this->directions = $this->getData('directions');
        $this->bitrix_directions = $this->getData('bitrix_directions');
        $this->sections = $this->repDirections::getAllSections();
    }

    public function process()
    {
        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_DIRECTIONS'));

        if ((int)$this->getField('lastId') === 0) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_DIRECTIONS'));
        }

        $progress = $this->save();
        $this->setProgress($progress);

        if ($progress >= 100) {
            if (!empty($this->directions)) {
                DirectionsRepository::resort();
            }

            $this->nextStep('DeactivateDirections');
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_DIRECTIONS_SUCCESS'));
        }
    }

    protected function save(): int
    {
        if (empty($this->directions)) {
            return 100;
        }

        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $cnt = 0;

        foreach ($this->directions as $key => &$item) {
            $cnt++;
            if ($item['bitrixId'])
                continue;

            $id = $this->create($item);

            if ($id) {
                $item['bitrixId'] = $id;
            } else {
                unset($this->directions[$key]);
            }

            if (
                $maxInterval &&
                time() - $time > $maxInterval
            ) {
                break;
            }
        }

        $this->setData('directions', $this->directions);
        $this->setData('bitrix_directions', $this->bitrix_directions);

        return intval($cnt * 100 / count($this->directions));
    }

    protected function create(array $item): ?int
    {
        if ($item['bitrixId']) {
            return $item['bitrixId'];
        }

        if ($item['parent']) {
            $parentId = $this->findParent((int)$item['parent']);
            if (!$parentId) {
                return null;
            }

            $item['parent'] = $parentId;
        }

        $section = $this->sections[$item['id']];
        $id = (int)$section['ID'];

        if ($id) {
            if (
                $section['NAME'] !== $item['name'] ||
                $section['IBLOCK_SECTION_ID'] !== $item['parent']
            ) {
                $this->repDirections->update($id, $item);
            }
        } else {
            $id = $this->repDirections->add($item);
        }

        unset($this->bitrix_directions[$id]);

        return $id;
    }

    protected function findParent(int $id): ?int
    {
        if (isset($this->directions[$id])) {
            $item = &$this->directions[$id];
            $id = $this->create($item);

            if ($id) {
                $item['bitrixId'] = $id;
            } else {
                unset($this->directions[$id]);
            }

            return $id;
        }

        $section = $this->sections[$id];

        if (!empty($section)) {
           return (int)$section['ID'];
        }

        return null;
    }
}
