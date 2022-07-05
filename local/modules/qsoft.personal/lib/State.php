<?php

namespace Qsoft\Personal;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;

class State
{
    private $arState = [];
    private $stateId = null;
    private static $instances = [];

    /**
     * @param string $id
     * @return State
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getInstance(string $id): self
    {
        if (!isset(self::$instances[$id])) {
            self::$instances[$id] = new self($id);
        }

        return self::$instances[$id];
    }

    /**
     * State constructor.
     * @param string $id
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected function __construct(string $id)
    {
        $this->stateId = $id;
        $this->loadState();
    }

    /**
     * @return string
     */
    protected function getStateId(): string
    {
        return $this->stateId;
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected function loadState()
    {
        $state = Option::get(MODULE_ID, 'state_'.strtolower($this->getStateId()), '');

        if (is_array($state))
            $this->arState = $state;
        elseif (!empty($state))
            $this->arState = unserialize($state);
        else
            $this->arState = [];
    }

    /**
     * @throws ArgumentOutOfRangeException
     */
    public function clearState()
    {
        $this->arState = [];
        Option::set(MODULE_ID, 'state_'.strtolower($this->getStateId()), '');
    }

    /**
     * @throws ArgumentOutOfRangeException
     */
    public function saveState()
    {
        Option::set(MODULE_ID, 'state_'.strtolower($this->getStateId()), serialize($this->arState));
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function getStateParam(string $param)
    {
        return $this->arState[$param];
    }

    /**
     * @param string $param
     * @param $value
     */
    public function setStateParam(string $param, $value)
    {
        $this->arState[$param] = $value;
    }

    /**
     * @throws ArgumentOutOfRangeException
     */
    protected function __destruct() {
        $this->saveState();
    }
}
