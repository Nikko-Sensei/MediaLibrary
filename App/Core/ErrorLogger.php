<?php

namespace App\Core;

use Throwable;

class ErrorLogger
{
    public static function log(Throwable $exception): void
    {
        $logDirectory = BASE_PATH . '/logs';

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0775, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log($message, 3, $logDirectory . '/error.log');
    }
}