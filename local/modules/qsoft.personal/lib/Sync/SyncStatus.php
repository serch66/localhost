<?php
namespace Qsoft\Personal\Sync;

use Qsoft\Personal\State;

class SyncStatus
{
    private const STATE_ID = 'sync_status';

    protected $isFinished = false;
    protected $message;
    protected $hasError = false;
    protected $errorMessage;
    protected $progress = 0;
    protected $data = [];
    protected $state = null;

    public function __construct()
    {
        $this->state = State::getInstance(self::STATE_ID);
    }

    public static function getLast(): self
    {
        $status = new self();
        $status->load();

        return $status;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data ?: [];
    }

    public function setError(string $message)
    {
        $this->hasError = true;
        $this->errorMessage = $message;
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function setFinished(bool $finished)
    {
        $this->isFinished = $finished;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?: '';
    }

    public function getMessage(): string
    {
        return $this->message ?: '';
    }

    public function setMessage(?string $message)
    {
        $this->message = $message;
    }

    public function getProgress(): int
    {
        return $this->progress ?: 0;
    }

    public function setProgress(int $percentage)
    {
        if ($percentage > 100)
            $percentage = 100;

        $this->progress = $percentage;
    }

    private function load()
    {
        $this->progress = (int)$this->state->getStateParam('progress');
        $this->message = $this->state->getStateParam('message');
        $this->errorMessage = $this->state->getStateParam('error');
        $this->isFinished = $this->state->getStateParam('finished');
        $this->hasError = !empty($this->errorMessage);
    }

    public function save()
    {
        $this->state->setStateParam('error', $this->errorMessage);
        $this->state->setStateParam('progress', $this->progress);
        $this->state->setStateParam('message', $this->message);
        $this->state->setStateParam('finished', $this->isFinished);
        $this->state->saveState();
    }

    private function clear()
    {
        $this->state->clearState();
    }
}
