<?php

namespace Scaleplan\Mailer\Exceptions;

/**
 * Class MailerException
 *
 * @package Scaleplan\Mailer
 */
class MailerException extends \Exception
{
    public const MESSAGE = 'Mailer error.';
    public const CODE = 500;

    /**
     * MailerException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: static::MESSAGE, $code, $previous);
    }
}
