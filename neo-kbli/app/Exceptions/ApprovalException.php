<?php

namespace App\Exceptions;

use Exception;

class ApprovalException extends Exception
{
    public static function notUnderReview(): self
    {
        return new self("Persetujuan hanya dapat diproses saat permohonan bersatus 'Sedang Ditinjau'. Silakan periksa status terkini permohonan.");
    }

    public static function reviewNotCompleted(): self
    {
        return new self("Persetujuan tidak dapat diproses karena peninjau aktif belum menyelesaikan evaluasinya. Pastikan ulasan terkait telah dikirimkan ke sistem.");
    }
}
