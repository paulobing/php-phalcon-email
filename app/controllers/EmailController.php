<?php

use Phalcon\Mvc\Controller;

include_once(__DIR__ . '/../helper/EmailHelper.php');
include_once(__DIR__ . '/../helper/QueueHelper.php');

require BASE_PATH . '/../vendor/autoload.php';

class EmailController extends Controller
{
    private $emailTo;
    /**
     * @var EmailHelper
     */
    private $emailHelper;
    /**
     * @var QueueHelper
     */
    private $queueHelper;

    public function initialize()
    {
        $this->emailHelper = new EmailHelper();
        $this->queueHelper = new QueueHelper();
        $this->emailTo = $this->emailHelper->getConfig('emailRecipient');
    }

    public function sendAsynchronousEmailAction()
    {
        $success = $this->queueHelper->queueEmail($this->emailTo);
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


}
