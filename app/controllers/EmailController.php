<?php

use Phalcon\Mvc\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

include_once(__DIR__ . '/../helper/EmailHelper.php');
include_once(__DIR__ . '/../helper/LoggerHelper.php');

require BASE_PATH . '/../vendor/autoload.php';

class EmailController extends Controller
{
    private $emailTo;
    private $emailHelper;
    private $logger;

    public function initialize()
    {
        $this->logger = LoggerHelper::getLogger('EmailController');
        $this->emailHelper = new EmailHelper();
        $this->emailTo = $this->emailHelper->getConfig('emailRecipient');
    }

    public function sendAsynchronousEmailAction()
    {
        $success = $this->queueEmail($this->emailTo);
        $this->handleSendEmailStatus($success, "Action executed successfully!
            Email will be sent to {$this->emailTo} !");
    }

    public function sendSynchronousEmailAction()
    {
        $success = $this->emailHelper->sendEmail($this->emailTo,
            '[SYNC] HappyCow - Thanks for clicking the button!',
            "<h1>HappyCow send email button</h1>
            <p>You've clicked the Synchronous process to send email.</p>"
        );
        $this->handleSendEmailStatus($success, "Email has been sent to {$this->emailTo}!");
    }

    private function handleSendEmailStatus($success, $message)
    {
        $this->view->success = $success;
        if ($success)
        {
            $this->view->message = $message;
        } else {
            $this->view->message = "Error sending email!";
        }
    }

    private function queueEmail(string $emailAddress): bool
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'myuser', 'mypassword');
        $channel = $connection->channel();
        $channel->queue_declare('emails', false, false, false, false);
        $msg = new AMQPMessage($emailAddress);
        $channel->basic_publish($msg, '', 'emails');
        $channel->close();
        try {
            $connection->close();
        } catch (Exception $e) {
            $this->logger->error('Error closing connection: ' . $e->getMessage());
            return false;
        }
        return true;
    }

}
