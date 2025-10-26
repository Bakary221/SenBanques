<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errors;

    public function __construct(
        string $message = 'Une erreur est survenue',
        int $statusCode = 400,
        ?string $errorCode = null,
        ?array $errors = null
    ) {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function render($request)
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if ($this->errorCode) {
            $response['error_code'] = $this->errorCode;
        }

        if ($this->errors) {
            $response['errors'] = $this->errors;
        }

        return response()->json($response, $this->statusCode);
    }
}