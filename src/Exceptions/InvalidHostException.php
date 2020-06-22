<?php

namespace Scaleplan\Mailer\Exceptions;

/**
 * Class InvalidHostException
 *
 * @package Scaleplan\Mailer
 */
class InvalidHostException extends MailerException
{
    public const MESSAGE = 'mailer.wrong-smtp';
    public const CODE = 406;
}
