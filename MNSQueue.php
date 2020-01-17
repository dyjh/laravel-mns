<?php

namespace Dyjh\LaravelMNS;

use AliyunMNS\Exception\MessageNotExistException;
use AliyunMNS\Requests\SendMessageRequest;
use Exception;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Dyjh\LaravelMNS\Adaptors\MNSAdapter;

class MNSQueue extends Queue implements QueueContract
{

    /**
     * @var MNSAdapter
     */
    protected $adapter;

    /**
     * The name of default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * @var null
     */
    private $waitSeconds;


    public function __construct(MNSAdapter $adapter, $queue, $waitSeconds = null)
    {
        $this->adapter     = $adapter;
        $this->default     = $queue;
        $this->connectionName     = $queue;
        $this->waitSeconds = $waitSeconds;
    }


    /**
     * Get the size of the queue.
     *
     * @param  string $queue
     *
     * @return int
     * @throws Exception
     */
    public function size($queue = null)
    {
        throw new Exception('The size method is not support for aliyun-mns');
    }


    /**
     * Push a new job onto the queue.
     *
     * @param string $job
     * @param mixed  $data
     * @param string $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        $payload = $this->createPayload($job, $data);

        return $this->pushRaw($payload, $queue);
    }


    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $message  = new SendMessageRequest($payload);
        $response = $this->adapter->useQueue($this->getQueue($queue))->sendMessage($message);

        return $response->getMessageId();
    }


    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param mixed         $data
     * @param string        $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        if (method_exists($this, 'getSeconds')) {
            $seconds = $this->getSeconds($delay);
        } else {
            $seconds = $this->secondsUntil($delay);
        }

        $payload  = $this->createPayload($job, $queue, $data);
        $message  = new SendMessageRequest($payload, $seconds);
        $response = $this->adapter->useQueue($this->getQueue($queue))->sendMessage($message);

        return $response->getMessageId();
    }

    /**
     * Pop the next job off of the queue.
     * @link https://help.aliyun.com/document_detail/35136.html
     *
     * @param string $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getDefaultIfNull($queue);

        try {
            $response = $this->adapter->useQueue($this->getQueue($queue))->receiveMessage($this->waitSeconds);
        } catch (MessageNotExistException $e) {
            $response = null;
        }

        if ($response) {
            return new Jobs\MNSJob($this->container, $this->adapter, $queue, $response);
        }

        return null;
    }


    /**
     * 获取默认队列名（如果当前队列名为 null）。
     *
     * @param string|null $wanted
     *
     * @return string
     */
    public function getDefaultIfNull($wanted)
    {
        return $wanted ? $wanted : $this->default;
    }


    /**
     * Get the queue or return the default.
     *
     * @param  string|null $queue
     *
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    public function getConnectionName()
    {
        return $this->connectionName;
    }
}
