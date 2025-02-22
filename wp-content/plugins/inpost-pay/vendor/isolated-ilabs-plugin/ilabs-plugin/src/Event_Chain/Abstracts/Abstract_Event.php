<?php

declare (strict_types=1);
namespace Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Abstracts;

use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Event_Chain;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Action_Interface;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Condition_Interface;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Event_Interface;
abstract class Abstract_Event implements Event_Interface
{
    /**
     * @var Action_Interface[]
     */
    protected $actions;
    /**
     * @var array
     */
    protected $arguments;
    /**
     * @var Condition_Interface[]|null
     */
    protected $conditions;
    /**
     * @var Event_Chain
     */
    protected $event_chain;
    /**
     * @return false|void
     */
    public function callback()
    {
        if (\is_array($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $condition->set_current_event($this);
                if ($condition->assert() === \false) {
                    return \false;
                }
            }
        }
        foreach ($this->actions as $action) {
            if ($action instanceof Action_Interface) {
                $action->set_current_event($this);
                $action->run();
            }
        }
    }
    public function get_actions() : array
    {
        return $this->actions;
    }
    public function set_actions(array $actions)
    {
        $this->actions = $actions;
    }
    /**
     * @return Condition_Interface[]|null
     */
    public function get_conditions() : ?array
    {
        return $this->conditions;
    }
    public function set_conditions(array $conditions)
    {
        $this->conditions = $conditions;
    }
    public abstract function create();
}
