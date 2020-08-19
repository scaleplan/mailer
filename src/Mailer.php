<?php
declare(strict_types=1);

namespace Scaleplan\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;
use Scaleplan\Mailer\Exceptions\InvalidHostException;
use Scaleplan\Mailer\Hooks\MailError;
use Scaleplan\Mailer\Hooks\MailSended;
use Scaleplan\Translator\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\Helpers\get_env;
use function Scaleplan\Helpers\get_required_env;
use function Scaleplan\Translator\translate;

/**
 * Класс отправки писем
 *
 * Class Mailer
 */
class Mailer implements MailerInterface
{
    /* Настроки PHPMailer */

    /**
     * Язык писем
     *
     * @var string
     */
    protected $mailLang = 'ru';

    /**
     * Кодировка писем
     *
     * @var string
     */
    protected $mailCharset = 'UTF-8';

    /**
     * Адрес SMTP-сервера
     *
     * @var string
     */
    protected $mailHost;

    /**
     * Логин для авторизации на SMTP-сервере
     */
    protected $mailUsername;

    /**
     * Пароль для авторизации на SMTP-сервере
     */
    protected $mailPassword;

    /**
     * Порт для подключения к SMTP-серверу
     */
    protected $mailPort = 465;

    /**
     * Обратный адрес писем
     */
    protected $mailFrom;

    /**
     * Имя отправителя
     */
    protected $mailFromName;

    /**
     * Куда присылать ответные письма
     */
    protected $mailReplyToAddress;

    /**
     * Кому отсылать ответные письма
     */
    protected $mailReplyToName;

    /**
     * Протокол безопасности
     */
    protected $mailSMTPSecure = 'ssl';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Mailer constructor.
     *
     * @param string $host
     * @param string|null $mailLang
     * @param string|null $mailFromName
     * @param string|null $mailFrom
     *
     * @throws InvalidHostException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(
        string $host,
        string $mailLang = null,
        string $mailFromName = null,
        string $mailFrom = null
    )
    {
        if (!filter_var(gethostbyname($host), FILTER_VALIDATE_IP)) {
            throw new InvalidHostException();
        }

        $this->mailLang = $mailLang ?? get_env('DEFAULT_MAIL_LANG') ?? $this->mailLang;
        $this->mailCharset = get_env('MAIL_CHARSET') ?? $this->mailCharset;
        $this->mailHost = get_required_env('MAIL_HOST');
        $this->mailUsername = get_required_env('MAIL_USERNAME');
        $this->mailPassword = get_required_env('MAIL_PASSWORD');
        $this->mailPort = get_env('MAIL_PORT') ?? $this->mailPort;
        $this->mailFrom = $mailFrom ?? get_env('MAIL_FROM') ?? "no-reply@$host";
        $this->mailFromName = $mailFromName ?? get_env('MAIL_FROM_NAME') ?? $this->mailFromName;
        $this->mailSMTPSecure = get_env('MAIL_SMTP_SECURE') ?? $this->mailSMTPSecure;

        $locale = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ? Translator::getRealLocale(
                \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']),
                get_required_env('BUNDLE_PATH') . get_required_env('TRANSLATES_PATH')
            )
            : get_required_env('DEFAULT_LANG');

        /** @var \Symfony\Component\Translation\Translator $translator */
        $translator = get_required_container(TranslatorInterface::class, [$locale]);
        $translator->addResource('yml', __DIR__ . "/translates/$locale/mailer.yml", $locale, 'mailer');
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getMailLang()
    {
        return $this->mailLang;
    }

    /**
     * @param mixed $mailLang
     */
    public function setMailLang($mailLang) : void
    {
        $this->mailLang = $mailLang;
    }

    /**
     * @return mixed
     */
    public function getMailCharset()
    {
        return $this->mailCharset;
    }

    /**
     * @param mixed $mailCharset
     */
    public function setMailCharset($mailCharset) : void
    {
        $this->mailCharset = $mailCharset;
    }

    /**
     * @return mixed
     */
    public function getMailHost()
    {
        return $this->mailHost;
    }

    /**
     * @param mixed $mailHost
     */
    public function setMailHost($mailHost) : void
    {
        $this->mailHost = $mailHost;
    }

    /**
     * @return mixed
     */
    public function getMailUsername()
    {
        return $this->mailUsername;
    }

    /**
     * @param mixed $mailUsername
     */
    public function setMailUsername($mailUsername) : void
    {
        $this->mailUsername = $mailUsername;
    }

    /**
     * @return mixed
     */
    public function getMailPassword()
    {
        return $this->mailPassword;
    }

    /**
     * @param mixed $mailPassword
     */
    public function setMailPassword($mailPassword) : void
    {
        $this->mailPassword = $mailPassword;
    }

    /**
     * @return mixed
     */
    public function getMailPort()
    {
        return $this->mailPort;
    }

    /**
     * @param mixed $mailPort
     */
    public function setMailPort($mailPort) : void
    {
        $this->mailPort = $mailPort;
    }

    /**
     * @return mixed
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @param mixed $mailFrom
     */
    public function setMailFrom($mailFrom) : void
    {
        $this->mailFrom = $mailFrom;
    }

    /**
     * @return mixed
     */
    public function getMailFromName()
    {
        return $this->mailFromName;
    }

    /**
     * @param mixed $mailFromName
     */
    public function setMailFromName($mailFromName) : void
    {
        $this->mailFromName = $mailFromName;
    }

    /**
     * @return mixed
     */
    public function getMailReplyToAddress()
    {
        return $this->mailReplyToAddress;
    }

    /**
     * @param mixed $mailReplyToAddress
     */
    public function setMailReplyToAddress($mailReplyToAddress) : void
    {
        $this->mailReplyToAddress = $mailReplyToAddress;
    }

    /**
     * @return mixed
     */
    public function getMailReplyToName()
    {
        return $this->mailReplyToName;
    }

    /**
     * @param mixed $mailReplyToName
     */
    public function setMailReplyToName($mailReplyToName) : void
    {
        $this->mailReplyToName = $mailReplyToName;
    }

    /**
     * @return mixed
     */
    public function getMailSMTPSecure()
    {
        return $this->mailSMTPSecure;
    }

    /**
     * @param mixed $mailSMTPSecure
     */
    public function setMailSMTPSecure($mailSMTPSecure) : void
    {
        $this->mailSMTPSecure = $mailSMTPSecure;
    }

    /**
     * @param string $message
     * @param array $context
     */
    protected function logOk(string $message, array $context = []) : void
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    protected function logError(string $message, array $context = []) : void
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->error($message, $context);
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
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function send(array $addresses, string $subject, string $message, array $files = []) : bool
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

        if ($this->mailReplyToAddress) {
            $mail->addReplyTo($this->mailReplyToAddress, $this->mailReplyToName ?? $this->mailReplyToAddress);
        }

        $mail->WordWrap = 50;

        foreach ($files as $file) {
            $mail->addAttachment($file);
        }

        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $message;

        if (!$mail->send()) {
            $this->logError(
                translate('mailer.error'),
                ['addresses' => $addresses, 'subject' => $subject, 'error' => $mail->ErrorInfo,]
            );
            MailError::dispatch(['mailer' => $mail,]);

            return false;
        }

        $this->logOk(translate('mailer.ok'), ['addresses' => $addresses, 'subject' => $subject]);
        MailSended::dispatch(['mailer' => $mail,]);

        return true;
    }
}
