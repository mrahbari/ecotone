<?php

namespace Messaging\Handler\Transformer;

use Messaging\Handler\MessageProcessor;
use Messaging\Handler\Processor\MethodInvoker\MethodInvoker;
use Messaging\Message;
use Messaging\Support\MessageBuilder;

/**
 * Class TransformerMessageProcessor
 * @package Messaging\Handler\Transformer
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @internal
 */
class TransformerMessageProcessor implements MessageProcessor
{
    /**
     * @var MethodInvoker
     */
    private $methodInvoker;

    /**
     * TransformerMessageProcessor constructor.
     * @param MethodInvoker $methodInvoker
     */
    private function __construct(MethodInvoker $methodInvoker)
    {
        $this->methodInvoker = $methodInvoker;
    }

    /**
     * @param MethodInvoker $methodInvoker
     * @return TransformerMessageProcessor
     */
    public static function createFrom(MethodInvoker $methodInvoker) : self
    {
        return new self($methodInvoker);
    }

    /**
     * @inheritDoc
     */
    public function processMessage(Message $message)
    {
        $reply = $this->methodInvoker->processMessage($message);
        $replyBuilder = MessageBuilder::fromMessage($message);

        if (is_null($reply)) {
            return null;
        }

        if (is_array($reply)) {
            if (is_array($message->getPayload())) {
                $reply = $replyBuilder
                    ->setPayload($reply)
                    ->build();
            }else {
                $reply = $replyBuilder
                    ->setMultipleHeaders($reply)
                    ->build();
            }
        }else if (!($reply instanceof Message)) {
            $reply = $replyBuilder
                ->setPayload($reply)
                ->build();
        }

        return $reply;
    }
}