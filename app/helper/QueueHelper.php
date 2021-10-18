<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

include_once('EmailHelper.php');
include_once('LoggerHelper.php');

class QueueHelper
{
    /**
     * @var Phalcon\Logger
     */
    private $logger;
    private $emailHelper;

    public function __construct()
    {
        $this->logger = LoggerHelper::getLogger('QueueHelper');
        $this->emailHelper = new EmailHelper();
    }

    public function queueEmail(string $emailAddress): bool
    {
        $this->logger->info('Publishing on Email Queue');
        $connection = new AMQPStreamConnection(RABBIT_HOSTNAME, 5672, 'myuser', 'mypassword');
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

    public function startEmailQueueConsumer()
    {
        $this->logger->info('Started Email Queue Consumer');
        $connection = new AMQPStreamConnection(RABBIT_HOSTNAME, 5672, 'myuser', 'mypassword');
        $channel = $connection->channel();
        $channel->queue_declare('emails', false, false, false, false);

        $callback = function ($msg) {
            $emailTo = $msg->body;
            $this->logger->info("Consuming queue.. emailTo: ${emailTo}");
            $this->emailHelper->sendEmail($emailTo,
                '[QUEUE] HappyCow - Thanks for clicking the button!',
                "<h1>HappyCow send email button</h1>
                <p>You've clicked the Async process to send email.</p>"
            );
        };

        $channel->basic_consume('emails', '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }
    }

}

