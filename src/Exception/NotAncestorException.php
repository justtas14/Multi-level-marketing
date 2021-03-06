<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotAncestorException extends HttpException
{
    public function __construct(
        string $message = 'User is not ancestor',
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        $statusCode = 403;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
