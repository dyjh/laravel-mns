<?php

namespace DYjh\LaravelMNS;

use Dyjh\LaravelMNS\Jobs\MNSJob;
use Illuminate\Support\Facades\Log;

class ReceiveExample
{
    /**
     * @param MNSJob $message
     */
    public function fire(MNSJob $message)
    {
        //TODO 业务逻辑
        Log::info("receive data：" . $message->getRawBody());
    }
}
