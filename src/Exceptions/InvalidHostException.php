<?php

namespace Scaleplan\Mailer\Exceptions;

/**
 * Class InvalidHostException
 *
 * @package Scaleplan\Mailer
 */
class InvalidHostException extends MailerException
{
    public const MESSAGE = 'Неверный хост отправки писем.';
    public const CODE = 406;
}
