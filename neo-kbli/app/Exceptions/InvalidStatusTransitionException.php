<?php

namespace App\Exceptions;

use Exception;
use App\Enums\ApplicationStatus;

class InvalidStatusTransitionException extends Exception
{
    public static function make(ApplicationStatus $from, ApplicationStatus $to): self
    {
        return new self("Tidak dapat mengubah status permohonan dari '{$from->value}' ke '{$to->value}'. Transisi ini melanggar aturan alur kerja. Silakan muat ulang halaman untuk melihat status terbaru.");
    }
}
