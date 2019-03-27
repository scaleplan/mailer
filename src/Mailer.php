<?php

namespace Scaleplan\Mailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Класс отправки писем
 *
 * Class Mailer
 * 
 * @package App\Classes
 */
class Mailer
{
    /* Настроки PHPMailer */

    /**
     * Язык писем
     */
    protected $mailLang = 'ru';

    /**
     * Кодировка писем
     */
    protected $mailCharset = 'UTF-8';

    /**
     * Адрес SMTP-сервера
     */
    protected $mailHost = 'smtp.yandex.ru';

    /**
     * Логин для авторизации на SMTP-сервере
     */
    protected $mailUsername = 'user@domain.com';

    /**
     * Пароль для авторизации на SMTP-сервере
     */
    protected $mailPassword = 'password';

    /**
     * Порт для подключения к SMTP-серверу
     */
    protected $mailPort = 465;

    /**
     * Обратный адрес писем
     */
    protected $mailFrom = 'user@domain.com';

    /**
     * Имя отправителя
     */
    protected $mailFromName = 'domain.com';

    /**
     * Куда присылать ответные письма
     */
    protected $mailReplyToAddress = 'user@domain.com';

    /**
     * Кому отсылать ответные письма
     */
    protected $mailReplyToName = 'domain.com';

    /**
     * Протокол безопасности
     */
    protected $mailSMTPSecure = 'ssl';

    /**
     * Mailer constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->mailLang = $config['mail_lang'] ?? $this->mailLang;
        $this->mailCharset = $config['mail_charset'] ?? $this->mailCharset;
        $this->mailHost = $config['mail_host'] ?? $this->mailHost;
        $this->mailUsername = $config['mail_username'] ?? $this->mailUsername;
        $this->mailPassword = $config['mail_password'] ?? $this->mailPassword;
        $this->mailPort = $config['mail_port'] ?? $this->mailPort;
        $this->mailFrom = $config['mail_from'] ?? $this->mailFrom;
        $this->mailFromName = $config['mail_from_name'] ?? $this->mailFromName;
        $this->mailReplyToAddress = $config['mail_reply_to_address'] ?? $this->mailReplyToAddress;
        $this->mailReplyToName = $config['mail_reply_to_name'] ?? $this->mailReplyToName;
        $this->mailSMTPSecure = $config['mail_smtp_secure'] ?? $this->mailSMTPSecure;
    }

    /**
     * Отправка почты
     *
     * @param array $addresses - массив адресов под рассылку
     * @param string $subject - тема письма
     * @param string $message - тело письма
     * @param array $files - прикрепляемые файлы
     *
     * @return bool
     *
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \ReflectionException
     */
    public function send(array $addresses, string $subject, string $message, array $files = []): bool
    {
        $mail = new PHPMailer();

        //$mail->SMTPDebug = 4;

        $reflector = new \ReflectionClass(PHPMailer::class);
        $mailerDir = \dirname($reflector->getFileName());

        $mail->setLanguage(
            $this->mailLang,
            "$mailerDir/language/phpmailer.lang-" . $this->mailLang . '.php'
        );
        $mail->CharSet = $this->mailCharset;
        $mail->isSMTP();
        $mail->Host = $this->mailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $this->mailUsername;
        $mail->Password = $this->mailPassword;
        $mail->SMTPSecure = $this->mailSMTPSecure;
        $mail->Port = $this->mailPort;

        $mail->From = $this->mailFrom;
        $mail->FromName = $this->mailFromName;

        foreach ($addresses as &$value) {
            $mail->addAddress($value);
        }

        unset($value);

        $mail->addReplyTo($this->mailReplyToAddress, $this->mailReplyToName);

        $mail->WordWrap = 50;

        foreach ($files as $file) {
            $mail->addAttachment($file);
        }

        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            return false;
        }

        return true;
    }
}
