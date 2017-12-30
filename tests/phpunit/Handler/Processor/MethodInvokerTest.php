<?php

namespace Messaging\Handler\Processor;

use Fixture\Service\ServiceExpectingMessageAndReturningMessage;
use Fixture\Service\ServiceExpectingOneArgument;
use Fixture\Service\ServiceExpectingThreeArguments;
use Fixture\Service\ServiceExpectingTwoArguments;
use Fixture\Service\ServiceWithoutAnyMethods;
use Messaging\Handler\Processor\MethodInvoker\HeaderArgument;
use Messaging\Handler\Processor\MethodInvoker\MethodInvoker;
use Messaging\Handler\Processor\MethodInvoker\PayloadArgument;
use Messaging\MessagingTest;
use Messaging\Support\InvalidArgumentException;
use Messaging\Support\MessageBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class MethodInvocationTest
 * @package Messaging\Handler\ServiceActivator
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class MethodInvokerTest extends MessagingTest
{
    public function test_throwing_exception_if_class_has_no_defined_method()
    {
        $this->expectException(InvalidArgumentException::class);

        MethodInvoker::createWith(ServiceWithoutAnyMethods::create(), 'getName', []);
    }

    public function test_throwing_exception_if_not_enough_arguments_provided()
    {
        $this->expectException(InvalidArgumentException::class);

        MethodInvoker::createWith(ServiceExpectingTwoArguments::create(), 'withoutReturnValue', []);
    }

    public function test_invoking_service()
    {
        $serviceExpectingOneArgument = ServiceExpectingOneArgument::create();

        $methodInvocation = MethodInvoker::createWith($serviceExpectingOneArgument, 'withoutReturnValue', [
            PayloadArgument::create('name')
        ]);

        $methodInvocation->processMessage(MessageBuilder::withPayload('some')->build());

        $this->assertTrue($serviceExpectingOneArgument->wasCalled(), "Method was not called");
    }

    public function test_invoking_service_with_return_value_from_header()
    {
        $serviceExpectingOneArgument = ServiceExpectingOneArgument::create();
        $headerName = 'token';
        $headerValue = '123X';

        $methodInvocation = MethodInvoker::createWith($serviceExpectingOneArgument, 'withReturnValue', [
            HeaderArgument::create('name', $headerName)
        ]);

        $this->assertEquals($headerValue,
            $methodInvocation->processMessage(
                MessageBuilder::withPayload('some')
                    ->setHeader($headerName, $headerValue)
                    ->build()
            )
        );
    }

    public function test_if_method_requires_one_argument_and_there_was_not_passed_any_then_use_payload_one_as_default()
    {
        $serviceExpectingOneArgument = ServiceExpectingOneArgument::create();

        $methodInvocation = MethodInvoker::createWith($serviceExpectingOneArgument, 'withReturnValue', []);

        $payload = 'some';

        $this->assertEquals($payload,
            $methodInvocation->processMessage(
                MessageBuilder::withPayload($payload)
                    ->build()
            )
        );
    }

    public function test_throwing_exception_if_passed_wrong_argument_names()
    {
        $serviceExpectingOneArgument = ServiceExpectingOneArgument::create();

        $this->expectException(InvalidArgumentException::class);

        MethodInvoker::createWith($serviceExpectingOneArgument, 'withoutReturnValue', [
            PayloadArgument::create('wrongName')
        ]);
    }

    public function test_invoking_service_with_multiple_not_ordered_arguments()
    {
        $serviceExpectingThreeArgument = ServiceExpectingThreeArguments::create();

        $methodInvocation = MethodInvoker::createWith($serviceExpectingThreeArgument, 'withReturnValue', [
            HeaderArgument::create('surname', 'personSurname'),
            HeaderArgument::create('age', 'personAge'),
            PayloadArgument::create('name'),
        ]);

        $this->assertEquals("johnybilbo13",
            $methodInvocation->processMessage(
                MessageBuilder::withPayload('johny')
                    ->setHeader('personSurname', 'bilbo')
                    ->setHeader('personAge', 13)
                    ->build()
            )
        );
    }
}