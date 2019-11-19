<?php

namespace Scaleplan\Mailer;

use Psr\Log\LoggerInterface;

/**
 * Класс отправки писем
 *
 * Class Mailer
 */
interface MailerInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void;

    /**
     * @return mixed
     */
    public function getMailLang();

    /**
     * @param mixed $mailLang
     */
    public function setMailLang($mailLang) : void;

    /**
     * @return mixed
     */
    public function getMailCharset();

    /**
     * @param mixed $mailCharset
     */
    public function setMailCharset($mailCharset) : void;

    /**
     * @return mixed
     */
    public function getMailHost();

    /**
     * @param mixed $mailHost
     */
    public function setMailHost($mailHost) : void;

    /**
     * @return mixed
     */
    public function getMailUsername();

    /**
     * @param mixed $mailUsername
     */
    public function setMailUsername($mailUsername) : void;

    /**
     * @return mixed
     */
    public function getMailPassword();

    /**
     * @param mixed $mailPassword
     */
    public function setMailPassword($mailPassword) : void;

    /**
     * @return mixed
     */
    public function getMailPort();

    /**
     * @param mixed $mailPort
     */
    public function setMailPort($mailPort) : void;

    /**
     * @return mixed
     */
    public function getMailFrom();

    /**
     * @param mixed $mailFrom
     */
    public function setMailFrom($mailFrom) : void;

    /**
     * @return mixed
     */
    public function getMailFromName();

    /**
     * @param mixed $mailFromName
     */
    public function setMailFromName($mailFromName) : void;

    /**
     * @return mixed
     */
    public function getMailReplyToAddress();

    /**
     * @param mixed $mailReplyToAddress
     */
    public function setMailReplyToAddress($mailReplyToAddress) : void;

    /**
     * @return mixed
     */
    public function getMailReplyToName();

    /**
     * @param mixed $mailReplyToName
     */
    public function setMailReplyToName($mailReplyToName) : void;

    /**
     * @return mixed
     */
    public function getMailSMTPSecure();

    /**
     * @param mixed $mailSMTPSecure
     */
    public function setMailSMTPSecure($mailSMTPSecure) : void;

    /**
     * Отправка почты
     *
     * @param array $addresses - массив адресов под рассылку
     * @param string $subject - тема письма
     * @param string $message - тело письма
     * @param array $files - прикрепляемые файлы
     *
     * @return bool
     */
    public function send(array $addresses, string $subject, string $message, array $files = []) : bool;
}
