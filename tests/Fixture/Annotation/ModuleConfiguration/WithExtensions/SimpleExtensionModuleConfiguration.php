<?php

namespace Fixture\Annotation\ModuleConfiguration\WithExtensions;

use SimplyCodedSoftware\IntegrationMessaging\Annotation\ModuleConfigurationExtensionAnnotation;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationConfiguration;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\ClassLocator;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\ClassMetadataReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Configuration;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationVariableRetrievingService;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;
use SimplyCodedSoftware\IntegrationMessaging\Config\ModuleConfigurationExtension;
use SimplyCodedSoftware\IntegrationMessaging\Annotation\ConfigurationVariableAnnotation;

/**
 * Class SimpleExtensionModuleConfiguration
 * @package Fixture\Annotation\ModuleConfiguration\WithExtensions
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 * @ModuleConfigurationExtensionAnnotation(moduleName="module-with-extension-configuration", variables={
 *      @ConfigurationVariableAnnotation(variableName="debug", description="debugging")
 * })
 */
class SimpleExtensionModuleConfiguration implements ModuleConfigurationExtension
{
    private $variables;

    /**
     * @inheritDoc
     */
    public static function create(ConfigurationVariableRetrievingService $configurationVariableRetrievingService): ModuleConfigurationExtension
    {
        $extension = new self();
        $extension->variables = ["debug" => $configurationVariableRetrievingService->get('debug')];

        return $extension;
    }

    public function getVariables() : array
    {
        return $this->variables;
    }
}