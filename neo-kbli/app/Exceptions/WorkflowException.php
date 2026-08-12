<?php

namespace App\Exceptions;

use Exception;

class WorkflowException extends Exception
{
    public function __construct(
        string $message,
        public string $fromState = '',
        public string $toState = ''
    ) {
        parent::__construct($message);
    }

    public static function invalidTransition(string $from, string $to): self
    {
        return new self(
            "Transisi status dari '{$from}' menjadi '{$to}' tidak diizinkan oleh sistem alur kerja. Silakan periksa kembali status permohonan Anda.",
            $from,
            $to
        );
    }

    /**
     * Get the exception's context information for logging.
     */
    public function context(): array
    {
        return [
            'workflow_from' => $this->fromState,
            'workflow_to' => $this->toState,
        ];
    }
}
