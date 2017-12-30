<?php

namespace Messaging\Handler\Transformer;
use Messaging\Handler\InputOutputMessageHandlerBuilder;
use Messaging\Handler\InterfaceToCall;
use Messaging\Handler\MessageHandlerBuilder;
use Messaging\Handler\MethodArgument;
use Messaging\Handler\Processor\MethodInvoker\MessageArgument;
use Messaging\Handler\Processor\MethodInvoker\MethodInvoker;
use Messaging\Handler\Processor\MethodInvoker\PayloadArgument;
use Messaging\Handler\RequestReplyProducer;
use Messaging\MessageChannel;
use Messaging\MessageHandler;
use Messaging\Support\InvalidArgumentException;
use Messaging\Support\MessageBuilder;

/**
 * Class TransformerBuilder
 * @package Messaging\Handler\Transformer
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class TransformerBuilder extends InputOutputMessageHandlerBuilder implements MessageHandlerBuilder
{
    /**
     * @var object
     */
    private $objectToInvoke;
    /**
     * @var string
     */
    private $methodName;
    /**
     * @var MethodArgument[]|array
     */
    private $methodArguments;

    /**
     * TransformerBuilder constructor.
     * @param MessageChannel $inputChannel
     * @param MessageChannel $outputChannel
     * @param $objectToInvoke
     * @param string $methodName
     * @param string $handlerName
     */
    private function __construct(MessageChannel $inputChannel, MessageChannel $outputChannel, $objectToInvoke, string $methodName, string $handlerName)
    {
        $this->objectToInvoke = $objectToInvoke;
        $this->methodName = $methodName;

        $this->withInputMessageChannel($inputChannel);
        $this->withOutputMessageChannel($outputChannel);
        $this->withName($handlerName);
    }

    /**
     * @param MessageChannel $inputChannel
     * @param MessageChannel $outputChannel
     * @param $objectToInvoke
     * @param string $methodName
     * @param string $handlerName
     * @return TransformerBuilder
     */
    public static function create(MessageChannel $inputChannel, MessageChannel $outputChannel, $objectToInvoke, string $methodName, string $handlerName): self
    {
        return new self($inputChannel, $outputChannel, $objectToInvoke, $methodName, $handlerName);
    }

    /**
     * @param string $handlerName
     * @param MessageChannel $inputChannel
     * @param MessageChannel $outputChannel
     * @param array $messageHeaders
     * @return TransformerBuilder
     */
    public static function createHeaderEnricher(string $handlerName, MessageChannel $inputChannel, MessageChannel $outputChannel, array $messageHeaders) : self
    {
        return new self($inputChannel, $outputChannel, HeaderEnricher::create($messageHeaders), "transform", $handlerName);
    }

    /**
     * @param array|MethodArgument[] $methodArguments
     * @return TransformerBuilder
     */
    public function withMethodArguments(array $methodArguments) : self
    {
       $this->methodArguments = $methodArguments;

       return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): MessageHandler
    {
        $interfaceToCall = InterfaceToCall::createFromObject($this->objectToInvoke, $this->methodName);
        $firstParameterName = $interfaceToCall->getFirstParameterName();
        $methodArguments = $this->methodArguments;

        if ($interfaceToCall->doesItReturnValue()) {
            throw InvalidArgumentException::create("Can't create transformer for {$interfaceToCall}, because method has no return value");
        }

        if (empty($methodArguments)) {
            if ($interfaceToCall->hasFirstParameterMessageTypeHint()) {
                $methodArguments[] = MessageArgument::create($firstParameterName);
            }else {
                $methodArguments[] = PayloadArgument::create($firstParameterName);
            }
        }

        return new Transformer(
            RequestReplyProducer::createFrom(
                $this->outputMessageChannel,
                TransformerMessageProcessor::createFrom(
                    MethodInvoker::createWith($this->objectToInvoke, $this->methodName, $methodArguments)
                ),
                $this->channelResolver,
                false
            )
        );
    }
}