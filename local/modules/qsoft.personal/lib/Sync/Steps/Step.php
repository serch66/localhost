<?php

namespace Qsoft\Personal\Sync\Steps;

use Qsoft\Personal\Logger\Logger;
use Qsoft\Personal\State;
use Qsoft\Personal\Sync\Manager;

abstract class Step implements StepInterface
{
    protected $manager = null;
    protected $isFinished = false;
    protected $message = '';
    protected $progress = 0;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    protected function getLogger(): Logger
    {
        return $this->manager->getLogger();
    }

    protected function nextStep($code)
    {
        $this->manager->setStep($code);
    }

    protected function getState(): State
    {
        return $this->manager->getState();
    }

    protected function getManager(): Manager
    {
        return $this->manager;
    }

    public function setData(string $key, ?array $data)
    {
        $this->manager->setData($key, $data);
    }

    public function getData(string $key)
    {
        return $this->manager->getData($key);
    }

    protected function setFinished(bool $finished)
    {
        $this->isFinished = $finished;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    protected function setMessage(?string $message)
    {
        $this->message = $message;
    }

    protected function setProgress(int $value)
    {
        $this->progress = $value;
    }

    public function getProgress(): int
    {
        return (int)$this->progress;
    }

    protected function getField(string $name)
    {
        $state = $this->getState();
        return $state->getStateParam($name);
    }

    protected function setField(string $name, $value)
    {
        $state = $this->getState();
        $state->setStateParam($name, $value);
    }

    abstract public function process();
}
