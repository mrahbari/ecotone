<?php

namespace Test\Ecotone\Modelling\Fixture\Annotation\CommandHandler\Aggregate;
use Ecotone\Messaging\Annotation\MessageToParameter\MessageToPayloadParameterAnnotation;
use Ecotone\Modelling\Annotation\Aggregate;
use Ecotone\Modelling\Annotation\AggregateIdentifier;
use Ecotone\Modelling\Annotation\CommandHandler;
use Ecotone\Modelling\Annotation\ReferenceCallInterceptorAnnotation;

/**
 * @Aggregate()
 */
class AggregateCommandHandlerWithRedirectionByChannelName
{
    /**
     * @var string
     * @AggregateIdentifier()
     */
    private $id;

    /**
     * @CommandHandler(inputChannelName="sameChannel", endpointId="factory")
     */
    public static function factory() : void
    {

    }

    /**
     * @CommandHandler(inputChannelName="sameChannel", endpointId="action")
     */
    public function action() : void
    {

    }
}