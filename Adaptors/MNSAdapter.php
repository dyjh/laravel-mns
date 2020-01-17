<?php

namespace App\server\LaravelMNS\Adaptors;

use AliyunMNS\AsyncCallback;
use AliyunMNS\Client;
use AliyunMNS\Model\QueueAttributes;
use AliyunMNS\Queue;
use AliyunMNS\Requests\BatchDeleteMessageRequest;
use AliyunMNS\Requests\BatchPeekMessageRequest;
use AliyunMNS\Requests\BatchReceiveMessageRequest;
use AliyunMNS\Requests\BatchSendMessageRequest;
use AliyunMNS\Requests\SendMessageRequest;
use AliyunMNS\Responses\BatchDeleteMessageResponse;
use AliyunMNS\Responses\BatchPeekMessageResponse;
use AliyunMNS\Responses\BatchReceiveMessageResponse;
use AliyunMNS\Responses\BatchSendMessageResponse;
use AliyunMNS\Responses\ChangeMessageVisibilityResponse;
use AliyunMNS\Responses\GetQueueAttributeResponse;
use AliyunMNS\Responses\MnsPromise;
use AliyunMNS\Responses\PeekMessageResponse;
use AliyunMNS\Responses\ReceiveMessageResponse;
use AliyunMNS\Responses\SendMessageResponse;
use AliyunMNS\Responses\SetQueueAttributeResponse;

/**
 * Class MNSAdapter
 *
 * @method string getUsing()
 * @method SetQueueAttributeResponse setAttribute( QueueAttributes $attributes )
 * @method MnsPromise setAttributeAsync( QueueAttributes $attributes, AsyncCallback $callback = null )
 * @method GetQueueAttributeResponse getAttribute( QueueAttributes $attributes )
 * @method MnsPromise getAttributeAsync( QueueAttributes $attributes, AsyncCallback $callback = null )
 * @method SendMessageResponse sendMessage( SendMessageRequest $request )
 * @method MnsPromise sendMessageAsync( SendMessageRequest $request, AsyncCallback $callback = null )
 * @method PeekMessageResponse peekMessage()
 * @method MnsPromise peekMessageAsync( AsyncCallback $callback = null )
 * @method ReceiveMessageResponse receiveMessage( int $waitSeconds = null )
 * @method MnsPromise receiveMessageAsync( AsyncCallback $callback = null )
 * @method ReceiveMessageResponse deleteMessage( string $receiptHandle )
 * @method MnsPromise deleteMessageAsync( string $receiptHandle, AsyncCallback $callback = null )
 * @method ChangeMessageVisibilityResponse changeMessageVisibility( string $receiptHandle, int $visibilityTimeout )
 * @method BatchSendMessageResponse batchSendMessage( BatchSendMessageRequest $request )
 * @method MnsPromise batchSendMessageAsync( BatchSendMessageRequest $request, AsyncCallback $callback = null )
 * @method BatchReceiveMessageResponse batchReceiveMessage( BatchReceiveMessageRequest $request )
 * @method MnsPromise batchReceiveMessageAsync( BatchReceiveMessageRequest $request, AsyncCallback $callback = null )
 * @method BatchPeekMessageResponse batchPeekMessage( BatchPeekMessageRequest $request )
 * @method MnsPromise batchPeekMessageAsync( BatchPeekMessageRequest $request, AsyncCallback $callback = null )
 * @method BatchDeleteMessageResponse batchDeleteMessage( BatchDeleteMessageRequest $request )
 * @method MnsPromise batchDeleteMessageAsync( BatchDeleteMessageRequest $request, AsyncCallback $callback = null )
 */
class MNSAdapter
{

    /**
     * Aliyun MNS Client
     *
     * @var Client
     */
    private $client;

    /**
     * Aliyun MNS SDK Queue.
     *
     * @var Queue
     */
    private $queue;
    private $connection;
    private $receiveController;

    private $using;


    public function __construct(Client $client, $connection, $receiveController)
    {
        $this->client     = $client;
        $this->connection = $connection;
        $this->receiveController = $receiveController;
    }

    public function getConnectionName()
    {
        return $this->connection;
    }

    public function getReceiveController()
    {
        return $this->receiveController;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([ $this->queue, $method ], $parameters);
    }


    /**
     * @param string $queue
     *
     * @return self
     */
    public function useQueue($queue)
    {
        if ($this->using != $queue) {
            $this->using = $queue;
            $this->queue = $this->client->getQueueRef($queue);
        }

        return $this;
    }
}
