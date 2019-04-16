<?php

namespace Scaleplan\Mailer\Exceptions;

/**
 * Class InvalidHostException
 *
 * @package Scaleplan\Mailer
 */
class InvalidHostException extends MailerException
{
    public const MESSAGE = 'Mailer host is invalid.';
}
