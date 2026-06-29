<?php

namespace App\Core;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $e): void
    {
        http_response_code(500);

        $message = APP_ENV === 'development'
            ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            : 'Internal Server Error';

        if (str_starts_with($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $message]);
        } else {
            require_once BASE_PATH . '/app/Views/errors/500.php';
        }
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}
