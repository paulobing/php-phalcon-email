<?php
use PHPMailer\PHPMailer\PHPMailer;

use Phalcon\Config\Adapter\Json;

require BASE_PATH . '/../vendor/autoload.php';

class EmailHelper
{
    private $logger;
    private $config;

    public function __construct()
    {
        $this->logger = LoggerHelper::getLogger('EmailHelper');

        $fileName = __DIR__ . '/../../config/config.json';
        $this->config = new Json($fileName);
    }

    public function sendEmail($emailTo, $subject, $mailContent): bool
    {
        $mail = new PHPMailer();

        $mail->Host = $this->getConfig('emailServer');
        $mail->Username = $this->getConfig('username');
        $mail->Password = $this->getConfig('password');
        if ($this->getConfig('smtp')) {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
        }
        $mail->Port = $this->getConfig('port');
        $mail->setFrom($this->getConfig('mailFrom'), $this->getConfig('mailFromName'));
        $mail->addReplyTo($this->getConfig('replyTo'), $this->getConfig('replyToName'));
        $mail->addAddress($emailTo, $emailTo);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $mailContent;

        if($mail->send()){
            return true;
        } else {
            $this->logger->error('Email could not be sent.');
            $this->logger->error('Mailer Error -- ' . $mail->ErrorInfo);
            return false;
        }

    }

    public function getConfig($configKey)
    {
        return $this->config->get('email')->get($configKey);
    }

}