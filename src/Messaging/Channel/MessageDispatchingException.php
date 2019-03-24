<?php
/**
 * Created by PhpStorm.
 * User: dgafka
 * Date: 14.10.17
 * Time: 14:29
 */

namespace SimplyCodedSoftware\Messaging\Channel;


use SimplyCodedSoftware\Messaging\MessagingException;

class MessageDispatchingException extends MessagingException
{
    /**
     * @inheritDoc
     */
    protected static function errorCode(): int
    {
        return self::MESSAGE_DISPATCHING_EXCEPTION;
    }
}