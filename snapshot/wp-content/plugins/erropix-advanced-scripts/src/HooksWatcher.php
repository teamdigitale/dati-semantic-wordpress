<?php

namespace ERROPiX\AdvancedScripts;

/**
 * Class HooksPromise
 * @package ERROPiX\AdvancedScripts
 */
class HooksWatcher
{
    /**
     * @var string[]
     */
    private $hooks;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param array $hooks 
     * @param callable $callback 
     * @return void 
     */
    public function __construct(array $hooks, callable $callback)
    {
        $this->hooks = array_flip($hooks);
        $this->callback = $callback;

        foreach ($hooks as $hook) {
            if (did_action($hook)) {
                unset($this->hooks[$hook]);
                continue;
            }

            add_action($hook, function () use ($hook) {
                unset($this->hooks[$hook]);
                $this->resolve();
            }, -1);
        }

        $this->resolve();
    }

    private function resolve()
    {
        if (empty($this->hooks)) {
            call_user_func($this->callback);
        }
    }
}
