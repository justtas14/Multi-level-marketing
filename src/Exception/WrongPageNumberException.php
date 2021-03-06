<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class WrongPageNumberException extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        $statusCode = 404;
        if (!$message) {
            $message = 'Page not found';
        }
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
