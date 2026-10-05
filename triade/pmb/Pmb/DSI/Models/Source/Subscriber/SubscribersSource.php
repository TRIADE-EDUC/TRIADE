<?php

namespace Pmb\DSI\Models\Source\Subscriber;

use Pmb\DSI\Models\Source\RootSource;

class SubscribersSource extends RootSource
{
    public $selector = null;

    public function __construct($selector)
    {
        if (!empty($selector->namespace) && class_exists($selector->namespace)) {
            $this->selector = new $selector->namespace($selector->data ?? null);
        }
    }

    public function getData()
    {
        return [];
    }
}
