<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundInvitationException extends HttpException
{
    public function __construct(
        string $message = null,
        \Exception $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        $statusCode = 404;
        if (!$message) {
            $message = 'Invitation not found';
        }
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
