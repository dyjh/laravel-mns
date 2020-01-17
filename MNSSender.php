<?php

namespace Dyjh\LaravelMNS;

use AliyunMNS\Client;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\SendMessageRequest;

class MNSSender
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string queueName
     */
    private $queue;

    /**
     * MNSSender constructor.
     * @param string $queueName
     */
    public function __construct(string $queueName = "iotSender")
    {
        $endPoint  = env('QUEUE_MNS_ENDPOINT');
        $accessId  = env('QUEUE_MNS_ACCESS_KEY');
        $accessKey = env('QUEUE_MNS_SECRET_KEY');
        $this->client = new Client($endPoint, $accessId, $accessKey);
        $this->queue = $queueName;
    }

    /**
     * send the message to the aliYun mns server
     *
     * @param string $messageBody
     * @return array
     */
    public function push(string $messageBody)
    {
        $queue = $this->client->getQueueRef($this->queue);
        $request = new SendMessageRequest($messageBody);
        try {
            $res = $queue->sendMessage($request);
            return ['result' => $res, 'message' => 'success'];
        } catch (MnsException $e) {
            return ['result' => false, 'message' => $e->getMessage()];
        }
    }
}
