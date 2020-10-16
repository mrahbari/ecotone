<?php
declare(strict_types=1);

namespace Ecotone\Messaging\Handler\Processor\MethodInvoker;

use Ecotone\Messaging\Handler\InterfaceToCall;
use Ecotone\Messaging\Handler\MessageHandlerBuilderWithOutputChannel;
use Ecotone\Messaging\Handler\MessageHandlerBuilderWithParameterConverters;
use Ecotone\Messaging\Handler\Processor\MethodInvoker\Converter\InterceptorConverterBuilder;
use Ecotone\Messaging\Handler\Processor\MethodInvoker\Converter\ReferenceBuilder;
use Ecotone\Messaging\Handler\TypeDefinitionException;
use Ecotone\Messaging\MessagingException;
use Ecotone\Messaging\Support\InvalidArgumentException;

/**
 * Class Interceptor
 * @package Ecotone\Messaging\Config
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class MethodInterceptor implements InterceptorWithPointCut
{
    private string $interceptorName;
    private \Ecotone\Messaging\Handler\MessageHandlerBuilderWithOutputChannel $messageHandler;
    private int $precedence;
    private \Ecotone\Messaging\Handler\Processor\MethodInvoker\Pointcut $pointcut;
    private \Ecotone\Messaging\Handler\InterfaceToCall $interceptorInterfaceToCall;

    /**
     * Interceptor constructor.
     * @param string $interceptorName
     * @param InterfaceToCall $interceptorInterfaceToCall
     * @param MessageHandlerBuilderWithOutputChannel $messageHandler
     * @param int $precedence
     * @param Pointcut $pointcut
     */
    private function __construct(string $interceptorName, InterfaceToCall $interceptorInterfaceToCall, MessageHandlerBuilderWithOutputChannel $messageHandler, int $precedence, Pointcut $pointcut)
    {
        $this->messageHandler = $messageHandler;
        $this->precedence = $precedence;
        $this->pointcut = $pointcut;
        $this->interceptorName = $interceptorName;
        $this->interceptorInterfaceToCall = $interceptorInterfaceToCall;
    }

    /**
     * @param string $interceptorName
     * @param InterfaceToCall $interceptorInterfaceToCall
     * @param MessageHandlerBuilderWithOutputChannel $messageHandler
     * @param int $precedence
     * @param string $pointcut
     */
    public static function create(string $interceptorName, InterfaceToCall $interceptorInterfaceToCall, MessageHandlerBuilderWithOutputChannel $messageHandler, int $precedence, string $pointcut): \Ecotone\Messaging\Handler\Processor\MethodInvoker\MethodInterceptor
    {
        return new self($interceptorName, $interceptorInterfaceToCall, $messageHandler, $precedence, Pointcut::createWith($pointcut));
    }

    /**
     * @param InterfaceToCall $interfaceToCall
     * @param object[] $endpointAnnotations
     * @return bool
     * @throws TypeDefinitionException
     * @throws MessagingException
     */
    public function doesItCutWith(InterfaceToCall $interfaceToCall, iterable $endpointAnnotations): bool
    {
        return $this->pointcut->doesItCut($interfaceToCall, $endpointAnnotations);
    }

    /**
     * @inheritDoc
     */
    public function getInterceptingObject(): object
    {
        return $this->messageHandler;
    }

    /**
     * @param InterfaceToCall $interceptedInterface
     * @param array $endpointAnnotations
     * @return static
     * @throws MessagingException
     * @throws InvalidArgumentException
     */
    public function addInterceptedInterfaceToCall(InterfaceToCall $interceptedInterface, array $endpointAnnotations): self
    {
        $clone = clone $this;
        $interceptedMessageHandler = clone $clone->messageHandler;

        if ($interceptedMessageHandler instanceof MessageHandlerBuilderWithParameterConverters) {
            $interceptedMessageHandler->withMethodParameterConverters(
                MethodInvoker::createDefaultMethodParameters(
                    $this->interceptorInterfaceToCall,
                    $interceptedMessageHandler->getParameterConverters(),
                    $endpointAnnotations,
                    $interceptedInterface,
                    false
                )
            );
        }
        $clone->messageHandler = $interceptedMessageHandler;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function hasName(string $name): bool
    {
        return $this->interceptorName === $name;
    }

    /**
     * @return string
     */
    public function getInterceptorName(): string
    {
        return $this->interceptorName;
    }

    /**
     * @return int
     */
    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->interceptorName}.{$this->messageHandler}";
    }

    /**
     * @return MessageHandlerBuilderWithOutputChannel
     */
    public function getMessageHandler(): MessageHandlerBuilderWithOutputChannel
    {
        return $this->messageHandler;
    }
}