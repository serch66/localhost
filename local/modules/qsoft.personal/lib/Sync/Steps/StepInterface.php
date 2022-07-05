<?php

namespace Qsoft\Personal\Sync\Steps;

use Qsoft\Personal\Sync\Manager;

interface StepInterface
{
    public function __construct(Manager $manager);
    public function setData(string $key, ?array $data);
    public function getData(string $key);
    public function isFinished(): bool;
    public function getMessage(): ?string;
    public function getProgress(): int;
    public function process();
}
