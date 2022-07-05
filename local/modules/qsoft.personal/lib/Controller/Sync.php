<?php

namespace Qsoft\Personal\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Type\DateTime;
use Qsoft\Personal\Debug;
use Qsoft\Personal\Sync\Manager;
use Qsoft\Personal\Sync\SyncStatus;

class Sync extends Controller
{
    /**
     * @return array
     */
    public function configureActions()
    {
        return [
            'start' => [
                'prefilters' => []
            ],
            'run' => [
                'prefilters' => []
            ],
            'stop' => [
                'prefilters' => []
            ],
        ];
    }

    public function startAction(string $from, int $maxTime)
    {
        $sync = new Manager(true);

        if ($sync::isRun(null, true)) {
            return new Error('Импорт уже выполняется');
        }

        $sync = new Manager(true);
        if ($from) {
            $sync->setDateFrom(new DateTime($from));
        }

        if ($maxTime) {
            $sync->setInterval($maxTime);
        }

        $status = $sync->start();

        return $this->getData($status);
    }

    public function runAction(bool $debug = false)
    {
        $obDebug = null;
        if ($debug) {
            $obDebug = Debug::getInstance();
            $obDebug->start();
        }

        $sync = new Manager(true);
        $status = $sync->next();

        return $this->getData($status, $obDebug);
    }

    public function stopAction()
    {
        $sync = new Manager(true);
        $sync->stop();

        return true;
    }

    protected function getData(SyncStatus $status, ?Debug $debug = null)
    {
        $data = [
            'status'     => $status->hasError() ? 'error' : 'success',
            'message'    => $status->hasError() ? $status->getErrorMessage() : $status->getMessage(),
            'percentage' => $status->getProgress(),
            'isFinished' =>  $status->isFinished(),
        ];

        if ($debug) {
            $debug->end();
            $data['debug'] = $debug->toArray();
        }

        return $data;
    }
}
