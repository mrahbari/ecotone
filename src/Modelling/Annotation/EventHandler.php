<?php

namespace Ecotone\Modelling\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Ecotone\Messaging\Annotation\EndpointAnnotation;

/**
 * Class EventHandler
 * @package Ecotone\Modelling\Annotation
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 * @Annotation
 * @Target({"METHOD"})
 */
class EventHandler extends EndpointAnnotation
{
    /**
     * @var array
     */
    public $parameterConverters = [];
    /**
     * if endpoint is not interested in message, set to true.
     *
     * @var string
     */
    public $ignoreMessage = false;
    /**
     * @var bool
     */
    public $filterOutOnNotFound = false;
    /**
     * Redirect to channel when factory method found already existing aggregate
     *
     * @var string
     */
    public $redirectToOnAlreadyExists = "";
    /**
     * @var string
     */
    public $outputChannelName = '';
    /**
     * Required interceptor reference names
     *
     * @var array
     */
    public $requiredInterceptorNames = [];
}