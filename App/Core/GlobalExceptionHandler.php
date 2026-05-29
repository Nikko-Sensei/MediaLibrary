<?php

namespace App\Core;

use App\DTO\ApiResponse;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use ErrorException;
use Throwable;

class GlobalExceptionHandler
{
    public static function register(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(Throwable $exception): void
    {
        ErrorLogger::log($exception);

        $statusCode = self::statusCodeFor($exception);

        if (self::isApiRequest()) {
            self::renderJsonResponse($exception, $statusCode);
            exit;
        }

        self::renderHtmlResponse($statusCode);
        exit;
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null || !self::isFatalError($error['type'])) {
            return;
        }

        self::handleException(
            new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            )
        );
    }

    private static function renderJsonResponse(
        Throwable $exception,
        int $statusCode
    ): void {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }

        $message = match (true) {
            $exception instanceof NotFoundException => $exception->getMessage(),
            $exception instanceof ValidationException => $exception->getMessage(),
            default => 'Something went wrong. Please try again later.'
        };

        $data = $exception instanceof ValidationException
            ? $exception->errors()
            : null;

        echo (new ApiResponse(false, $message, $data))->toJson();
    }

    private static function renderHtmlResponse(int $statusCode): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        $pageTitle = $statusCode === 404 ? 'Page not found' : 'Something went wrong';
        $section = '';
        $hideSearch = true;

        $view = $statusCode === 404
            ? BASE_PATH . '/view/errors/404.php'
            : BASE_PATH . '/view/errors/500.php';

        require $view;
    }

    private static function statusCodeFor(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof NotFoundException => 404,
            $exception instanceof ValidationException => 422,
            default => 500
        };
    }

    private static function isApiRequest(): bool
    {
        $page = $_GET['page'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return str_starts_with($page, 'api/')
            || str_contains($accept, 'application/json');
    }

    private static function isFatalError(int $type): bool
    {
        return in_array(
            $type,
            [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR],
            true
        );
    }
}
