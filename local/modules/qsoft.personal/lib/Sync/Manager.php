<?php

namespace Qsoft\Personal\Sync;

use Bitrix\Main;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Data\CacheEngineFiles;
use Bitrix\Main\Type\DateTime;
use Qsoft\Personal\Admin\Options;
use Qsoft\Personal\Exception\CreateStepException;
use Qsoft\Personal\Exception\Exception;
use Qsoft\Personal\Logger\Logger;
use Qsoft\Personal\State;
use Qsoft\Personal\Sync\Steps\Step;

use const Qsoft\Personal\MODULE_ID;

class Manager
{
    private const SYNC_MAX_TIME = 15;
    private const SYNC_DELAY = 1;
    private const STATE_ID = 'sync';

    protected $byStep = true;
    protected $state = null;
    protected $data = [];
    protected $runIteration = 0;
    protected $dateFrom = null;
    protected $syncChanges = false;
    protected $logger = null;
    protected $interval = 0;

    /**
     * Manager constructor.
     * @param int $serverId
     * @param bool $isTest
     * @param bool $byStep
     * @throws Main\ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function __construct(bool $byStep = true, ?Logger $logger = null)
    {
        $this->byStep = $byStep;
        $this->state = State::getInstance(self::STATE_ID);
        $this->logger = $logger ?? Logger::channel('empty');
    }

    /**
     * @return SyncStatus
     */
    public function start(): SyncStatus
    {
        $this->setStep($this->getFirstStep());
        $this->clearData();
        $this->state->clearState();

        $this->init();
        return $this->run();
    }

    /**
     * @return SyncStatus
     */
    public function next(): SyncStatus
    {
        $this->init();
        return $this->run();
    }

    public function init()
    {
        if ($this->interval) {
            $this->setInterval($this->interval);
        } elseif ($this->byStep) {
            $this->interval = (int)$this->state->getStateParam('interval');
            if (!$this->interval) {
                $this->setInterval(self::SYNC_MAX_TIME);
            }
        }

        if ($this->dateFrom) {
            $this->setDateFrom($this->dateFrom);
        } else {
            $dateFromTimestamp = (int)$this->state->getStateParam('dateFrom');
            if ($dateFromTimestamp) {
                $this->setDateFrom(DateTime::createFromTimestamp($dateFromTimestamp));
            }
        }
    }

    public function setDateFrom(?DateTime $date)
    {
        $this->dateFrom = $date;
        $this->syncChanges = !empty($date);
        $this->state->setStateParam('dateFrom', $date->getTimestamp());
    }

    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    public function setInterval(int $interval)
    {
        $this->interval = $interval;
        $this->state->setStateParam('interval', $interval);
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function syncChanges(): bool
    {
        return $this->syncChanges;
    }

    protected static function getAllSteps(): array
    {
        return [
            'Init'                  => __NAMESPACE__ . '\Steps\Init',
            'LoadUsers'             => __NAMESPACE__ . '\Steps\LoadUsers',
            'LoadDirections'        => __NAMESPACE__ . '\Steps\LoadDirections',
            'SaveDirections'        => __NAMESPACE__ . '\Steps\SaveDirections',
            'DeactivateDirections'  => __NAMESPACE__ . '\Steps\DeactivateDirections',
            'SaveUsers'             => __NAMESPACE__ . '\Steps\SaveUsers',
            'DeactivateUsers'       => __NAMESPACE__ . '\Steps\DeactivateUsers',
            'SaveHeads'             => __NAMESPACE__ . '\Steps\SaveHeads',
            'Finish'                => __NAMESPACE__ . '\Steps\Finish'
        ];
    }

    public function setStep(?string $newValue = null)
    {
        $allSteps = static::getAllSteps();

        if (!empty($newValue) && isset($allSteps[$newValue])) {
            $this->state->setStateParam('step', $newValue);
        }
    }

    public function getStep(): string
    {
        $step = $this->state->getStateParam('step');

        if (empty($step)) {
            $step = $this->getFirstStep();
        }

        return $step;
    }

    public function getFirstStep(): string
    {
        $stepKeys = array_keys(static::getAllSteps());
        return reset($stepKeys);
    }

    /**
     * @return Steps\Step
     * @throws CreateStepException
     */
    protected function getStepInstance(): Steps\Step
    {
        $allSteps = static::getAllSteps();
        $code = $this->getStep();

        if (isset($allSteps[$code])) {
            $class = $allSteps[$code];
            if (class_exists($class)) {
                return new $class($this);
            }
        }

        throw new CreateStepException("Step $code not found");
    }

    /**
     * @throws ArgumentOutOfRangeException
     * @throws Main\ArgumentNullException
     */
    public function stop()
    {
        $this->setStep($this->getFirstStep());
        $this->clearData();
        self::isRun(false, $this->byStep);
    }

    public function getState(): State
    {
        return $this->state;
    }

    protected function canWork(bool $isFinished): bool
    {
        if ($isFinished) {
            return false;
        }

        if (
            $this->byStep &&
            $this->runIteration
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return SyncStatus
     */
    protected function run(): SyncStatus
    {
        session_write_close(); //закрываем запись в сессию, чтобы не блокировать запросы

        $status = new SyncStatus();
        $bFinish = false;

        try {
            while ($this->canWork($bFinish)) {
                self::isRun(true, $this->byStep);

                $this->runIteration++;
                $allProgress = $this->getProgressPercentage();

                $step = $this->getStepInstance();
                $step->process();

                $status->setMessage($step->getMessage());

                $bFinish = $step->isFinished();
                if ($bFinish) {
                    $status->setProgress(100);
                    break;
                } else {
                    $status->setProgress(
                        $allProgress + $step->getProgress() / count(self::getAllSteps())
                    );
                }

                if (!$this->byStep) {
                    sleep(self::SYNC_DELAY);
                }

                $status->save();
            }

            if ($bFinish) {
                Options::setLastSyncDate(new DateTime());
            } else {
                $this->state->saveState();
            }
        } catch (Exception $e) {
            $status->setError($e->getMessage());
            $this->getLogger()->error($e->getMessage(), [$e->getTraceAsString()]);
            $bFinish = true;
        } catch (\Throwable $e) {
            $status->setError($e->getMessage());
            $this->getLogger()->critical($e->getMessage(), [$e->getTraceAsString()]);
            $bFinish = true;
        }

        $status->setFinished($bFinish);

        if ($bFinish) {
            self::isRun(false, $this->byStep);
        }

        $this->saveData();

        return $status;
    }

    public function getProgressPercentage(): int
    {
        $index = 0;
        $current = $this->getStep();
        $allSteps = static::getAllSteps();

        foreach ($allSteps as $code => $class) {
            if ($current === $code) {
                break;
            }
            $index++;
        }

        return floor($index / count($allSteps) * 100);
    }

    protected function getCache(array $params = [])
    {
        $cacheEngine = new CacheEngineFiles($params);
        return new Main\Data\Cache($cacheEngine);
    }

    protected function getCacheKey(string $ext = '')
    {
        return MODULE_ID . '_cache_' . $ext;
    }

    protected function getCacheDir()
    {
        return MODULE_ID . '_sync';
    }

    protected function clearData()
    {
        $cache = $this->getCache();
        $cache->cleanDir($this->getCacheDir());
        $this->data = [];
    }

    public function setData(string $key, ?array $data)
    {
        $this->data[$key] = $data;
    }

    protected function saveData()
    {
        if (is_array($this->data)) {
            foreach ($this->data as $key => $data) {
                $cache = $this->getCache();
                $cache->forceRewriting(true);
                $cache->initCache(36000, $this->getCacheKey($key), $this->getCacheDir());
                if ($data !== null) {
                    $cache->startDataCache(36000, $this->getCacheKey($key));
                    $cache->endDataCache($data);
                } else {
                    $cache->abortDataCache();
                }
            }
        }
    }

    public function getData(string $key): ?array
    {
        if (!isset($this->data[$key])) {
            $cache = $this->getCache();
            if ($cache->initCache(36000, $this->getCacheKey($key), $this->getCacheDir())) {
                $vars = $cache->getVars();
                $this->data[$key] = $vars;
            }
        }
        return $this->data[$key];
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Помечает что импорт выполянется
     */
    public static function isRun(?bool $set = null, bool $byStep = false): bool
    {
        $name = 'last_sync_activity' . ($byStep ? '_steps' : '_cron');

        if ($set === true) {
            Option::set(
                MODULE_ID,
                $name,
                time()
            );
        } elseif ($set === false) {
            Option::set(
                MODULE_ID,
                $name,
                0
            );
        }

        $time = Option::get(
            MODULE_ID,
            $name,
            0
        );

        $maxTime = $byStep ? 15 : 1800;

        return time() - $time < $maxTime;
    }
}
