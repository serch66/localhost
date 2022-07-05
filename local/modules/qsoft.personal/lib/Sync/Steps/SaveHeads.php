<?php

namespace Qsoft\Personal\Sync\Steps;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Qsoft\Personal\Sync\Manager;

Loc::loadMessages(__FILE__);

class SaveHeads extends Step
{
    protected const SYNC_STEP_SIZE = 50;

    protected $directions = [];

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->directions = $this->getData('directions');
    }

    public function process()
    {
        Loader::includeModule('iblock');

        $this->setMessage(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_HEADS'));

        if ((int)$this->getField('start') === 0) {
            $this->setField('start', 1);
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_HEADS'));
        }

        $progress = $this->save();
        $this->setProgress($progress);

        if ($progress === 100) {
            $this->getLogger()->info(Loc::getMessage('QSOFT_PERSONAL_SYNC_SAVE_HEADS_SUCCESS'));
            $this->setProgress(100);
            $this->nextStep('Finish');
        }
    }

    /**
     * @return bool
     */
    protected function save(): int
    {
        if (empty($this->directions)) {
            return 100;
        }

        $time = time();
        $maxInterval = $this->getManager()->getInterval();
        $count = 0;
        $section = new \CIBlockSection();

        while ($items = $this->getItems()) {
            $users = $this->findUsers(array_values($items));

            foreach($items as $sectionId => $userXmlId) {
                $section->Update(
                    $sectionId,
                    [
                        'UF_HEAD' => intval($users[$userXmlId])
                    ]
                );

                $count++;
            }

            if (
                $maxInterval &&
                time() - $time > $maxInterval
            ) {
                break;
            }
        }

        $this->setData('directions', $this->directions);

        if (empty($this->directions))
            return 100;

        return intval($count * 100 / count($this->directions));
    }

    protected function getItems(): array
    {
        $items = [];

        foreach ($this->directions as $key => $item) {
            unset($this->directions[$key]);

            if (!$item['bitrixId']) {
                continue;
            }

            $items[$item['bitrixId']] = (int)$item['chief']['id'];

            if (count($items) >= self::SYNC_STEP_SIZE) {
                break;
            }
        }

        return $items;
    }

    protected function findUsers(array $xmlIds): array
    {
        if (empty($xmlIds)) return [];

        $users = UserTable::getList(
            [
                'filter' => ['=XML_ID' => $xmlIds],
                'select' => ['ID', 'XML_ID']
            ]
        )->fetchAll();

        return array_column($users, 'ID', 'XML_ID');
    }
}
