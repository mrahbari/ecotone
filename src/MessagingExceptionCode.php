<?php

namespace Messaging;

/**
 * Interface ContainsMessagingExceptionCode
 * @package Messaging\Exception
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
interface MessagingExceptionCode
{
    const INVALID_MESSAGE_HEADER = 100;
    const MESSAGE_HEADER_DOES_NOT_EXISTS = 101;
    const INVALID_ARGUMENT = 102;

    const MESSAGE_SEND_EXCEPTION = 200;
    const MESSAGE_DISPATCHING_EXCEPTION = 201;
    const WRONG_HANDLER_AMOUNT = 201;
}