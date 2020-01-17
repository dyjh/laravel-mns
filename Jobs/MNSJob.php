<?php

namespace Dyjh\LaravelMNS\Jobs;

use AliyunMNS\Responses\ReceiveMessageResponse;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Dyjh\LaravelMNS\Adaptors\MNSAdapter;
use Illuminate\Support\Facades\Log;

class MNSJob extends Job implements JobContract
{

    /**
     * The class name of the job.
     *
     * @var string
     */
    protected $job;

    /**
     * The queue message data.
     *
     * @var string
     */
    protected $data;

    /**
     * @var MNSAdapter
     */

    private $adapter;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container              $container
     * @param MNSAdapter                                   $mns
     * @param string                                       $queue
     * @param \AliyunMNS\Responses\ReceiveMessageResponse  $job
     */
    public function __construct(Container $container, MNSAdapter $mns, $queue, ReceiveMessageResponse $job)
    {
        $this->container          = $container;
        $this->adapter            = $mns;
        $this->queue              = $queue;
        $this->connectionName     = $mns->getConnectionName();
        $this->job                = $job;
    }


    /**
     * Fire the job.
     */
    public function fire()
    {
        Log::info($this->getRawBody());
        $payload = json_decode($this->getRawBody(), true);
        Log::info($payload);
        print_r($payload);
        parent::fire();
    }


    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        $message = $this->job->getMessageBody();
        if ($res = $this->isJson($message)) {
            $message = $res;
        }
        $data = [
            'job'     => $this->adapter->getReceiveController(),
            'queue'   => $this->queue,
            'data'    => $message
        ];
        return json_encode($data);
    }

    private function isJson($data = '', $assoc = false)
    {
        $data = json_decode($data, $assoc);
        if (($data && is_object($data)) || (is_array($data) && !empty($data))) {
            return $data;
        }
        return false;
    }

    /**
     * Delete the job from the queue.
     */
    public function delete()
    {
        parent::delete();
        $receiptHandle = $this->job->getReceiptHandle();
        $this->adapter->deleteMessage($receiptHandle);
    }


    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     */
    public function release($delay = 1)
    {
        parent::release($delay);

        if ($delay < 1) {
            $delay = 1;
        }

        $this->adapter->changeMessageVisibility($this->job->getReceiptHandle(), $delay);
    }


    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job->getDequeueCount();
    }


    /**
     * Get the IoC container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }


    /**
     * Get the Job ID.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->getMessageId();
    }
}
