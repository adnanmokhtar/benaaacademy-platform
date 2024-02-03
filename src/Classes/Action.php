<?php

namespace Benaaacademy\Platform\Classes;

use Illuminate\Events\Dispatcher;
use Illuminate\View\View;


/*
 * Class BenaaacademyAction
 * @package Benaaacademy\Platform
 */
class Action extends Dispatcher
{

    /*
     * @param $event
     * @param array $payload
     * @param bool $halt
     * @return bool
     */
    public function fire($event, $payload = [], $halt = false)
    {

        $output = $this->dispatch($event, $payload, $halt);

        if ($halt) {
            return [];
        }

        $outputs = [];

        foreach ($output as $value) {

            if ($value instanceof View) {
                $outputs[] =  $value->render();
            } else {
                $outputs[] = $value;
            }

        }

        return $outputs;
    }

}
