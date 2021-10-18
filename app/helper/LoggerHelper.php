<?php

use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class LoggerHelper
{
    public static function getLogger($loggerName): Logger
    {
        $adapter = new Stream('php://stderr');
        return new Logger(
            $loggerName,
            [
                'main' => $adapter,
            ]
        );
    }
}
