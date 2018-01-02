<?php

namespace Messaging\Config;

use Messaging\Endpoint\ConsumerEndpointFactory;
use Messaging\Endpoint\PollableConsumerFactory;
use Messaging\Handler\MessageHandlerBuilder;
use Messaging\MessageChannel;

/**
 * Class MessagingSystemConfiguration
 * @package Messaging\Config
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
final class MessagingSystemConfiguration implements Configuration
{
    /**
     * @var NamedMessageChannel[]
     */
    private $namedChannels = [];
    /**
     * @var MessageHandlerBuilder[]
     */
    private $messageHandlerBuilders = [];
    /**
     * @var PollableConsumerFactory
     */
    private $pollableFactory;

    /**
     * @return MessagingSystemConfiguration
     */
    public static function prepare() : self
    {
        return new self();
    }

    /**
     * @param string $messageChannelName
     * @param MessageChannel $messageChannel
     * @return MessagingSystemConfiguration
     */
    public function registerMessageChannel(string $messageChannelName, MessageChannel $messageChannel): self
    {
        $this->namedChannels[] = NamedMessageChannel::create($messageChannelName, $messageChannel);

        return $this;
    }

    /**
     * @param MessageHandlerBuilder $messageHandlerBuilder
     * @return MessagingSystemConfiguration
     */
    public function registerMessageHandler(MessageHandlerBuilder $messageHandlerBuilder) : self
    {
        $this->messageHandlerBuilders[] = $messageHandlerBuilder;

        return $this;
    }

    /**
     * @param PollableConsumerFactory $pollableFactory
     * @return MessagingSystemConfiguration
     */
    public function setPollableFactory(PollableConsumerFactory $pollableFactory) : self
    {
        $this->pollableFactory = $pollableFactory;

        return $this;
    }

    /**
     * Initialize messaging system from current configuration.
     * This is one time process, after initialization you won't be able to configure messaging system anymore.
     *
     * @return MessagingSystem
     */
    public function buildMessagingSystemFromConfiguration() : MessagingSystem
    {
        $channelResolver = InMemoryChannelResolver::create($this->namedChannels);
        $consumerEndpointFactory = new ConsumerEndpointFactory($channelResolver, $this->pollableFactory);
        $consumers = [];

        foreach ($this->messageHandlerBuilders as $messageHandlerBuilder) {
            $consumers[] = $consumerEndpointFactory->create($messageHandlerBuilder);
        }

        return MessagingSystem::create($consumers, $channelResolver);
    }

    /**
     * Only one instance at time
     *
     * MessagingSystemConfiguration constructor.
     */
    private function __construct()
    {
    }

    /**
     * Only one instance at time
     *
     * @internal
     */
    private function __clone()
    {

    }
}