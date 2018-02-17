<?php

namespace Test\SimplyCodedSoftware\IntegrationMessaging;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SimplyCodedSoftware\IntegrationMessaging\Message;
use SimplyCodedSoftware\IntegrationMessaging\MessageHeaders;
use SimplyCodedSoftware\IntegrationMessaging\Support\MessageCompareService;

/**
 * Class MessagingTest
 * @package SimplyCodedSoftware\IntegrationMessaging
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
abstract class MessagingTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/../Fixture';

    public function assertMessages(Message $message, Message $toCompareWith) : void
    {
        if (!MessageCompareService::areSameMessagesIgnoringIdAndTimestamp($message, $toCompareWith)) {
            $this->assertEquals($message, $toCompareWith);
        }else {
            $this->assertTrue(true);
        }
    }

    public function assertMultipleMessages(array $messages, array $messagesToCompareWith) : void
    {
        $messagesAmount = count($messages);
        Assert::assertCount($messagesAmount, $messagesToCompareWith, "Amount of messages is different");

        for ($i = 0; $i < $messagesAmount; $i++) {
            $this->assertMessages($messages[$i], $messagesToCompareWith[$i]);
        }
    }
}