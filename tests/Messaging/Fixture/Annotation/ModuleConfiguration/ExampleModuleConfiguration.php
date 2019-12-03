<?php
declare(strict_types=1);

namespace Test\Ecotone\Messaging\Fixture\Annotation\ModuleConfiguration;

use Ecotone\Messaging\Annotation\ModuleAnnotation;
use Ecotone\Messaging\Config\Configuration;
use Ecotone\Messaging\Config\ModuleReferenceSearchService;
use Ecotone\Messaging\Handler\MessageHandlerBuilder;
use Ecotone\Messaging\Handler\ReferenceSearchService;

/**
 * Class ExampleModuleConfiguration
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @ModuleAnnotation()
 */
class ExampleModuleConfiguration implements \Ecotone\Messaging\Config\Annotation\AnnotationModule
{
    /**
     * @var array
     */
    private $extensionObjects;
    /**
     * @var MessageHandlerBuilder[]
     */
    private $messageHandlers = [];

    private function __construct(array $extensionObjects)
    {
        $this->extensionObjects = $extensionObjects;
    }

    public static function createEmpty(): self
    {
        return new self([]);
    }

    /**
     * @param array $extensionObjects
     * @return ExampleModuleConfiguration
     */
    public static function createWithExtensions(array $extensionObjects): self
    {
        return new self($extensionObjects);
    }

    public static function createWithHandlers(iterable $messageHandlerBuilders) : self
    {
        $self = self::createEmpty();
        $self->messageHandlers = $messageHandlerBuilders;

        return $self;
    }

    /**
     * @inheritDoc
     */
    public static function create(\Ecotone\Messaging\Config\Annotation\AnnotationRegistrationService $annotationRegistrationService): \Ecotone\Messaging\Config\Annotation\AnnotationModule
    {
        return new self([]);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "exampleModule";
    }

    /**
     * @inheritDoc
     */
    public function prepare(Configuration $configuration, array $extensionObjects, ModuleReferenceSearchService $moduleReferenceSearchService): void
    {
        $this->extensionObjects = $extensionObjects;

        foreach ($this->messageHandlers as $messageHandlerBuilder) {
            $configuration->registerMessageHandler($messageHandlerBuilder);
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public function getRelatedReferences(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function canHandle($extensionObject): bool
    {
        return $extensionObject instanceof \stdClass;
    }

    /**
     * @inheritDoc
     */
    public function afterConfigure(ReferenceSearchService $referenceSearchService): void
    {
        // TODO: Implement registerWithin() method.
    }


}