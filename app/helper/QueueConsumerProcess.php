<?php

define('BASE_PATH', dirname(__DIR__));
define('RABBIT_HOSTNAME', getenv('RABBIT_HOSTNAME') ?: 'localhost');

include_once('QueueHelper.php');

(new QueueHelper())->startEmailQueueConsumer();
