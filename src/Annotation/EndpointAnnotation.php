<?php
declare(strict_types=1);

namespace SimplyCodedSoftware\IntegrationMessaging\Annotation;
use Ramsey\Uuid\Uuid;

/**
 * Class EndpointAnnotation
 * @package SimplyCodedSoftware\IntegrationMessaging\Annotation
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
abstract class EndpointAnnotation
{
    /**
     * @var string
     */
    public $endpointId;
    /**
     * @var string
     * @Required()
     */
    public $inputChannelName;
    /**
     * @var Poller|null
     */
    public $poller;

    /**
     * EndpointAnnotation constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $propertyName => $value) {
            $this->{$propertyName} = $value;
        }

        if (!$this->endpointId) {
            $this->endpointId = Uuid::uuid4()->toString();
        }
    }
}