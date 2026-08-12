<?php

namespace App\Exceptions;

use Exception;

class ReviewException extends Exception
{
    public static function alreadySubmitted(): self
    {
        return new self("Ulasan ini telah dikirimkan dan tidak dapat diubah kembali. Silakan hubungi administrator jika terjadi kesalahan.");
    }

    public static function notInReviewState(): self
    {
        return new self("Permohonan ini tidak berada dalam tahap ulasan. Silakan periksa status permohonan saat ini.");
    }

    public static function unauthorizedReviewer(): self
    {
        return new self("Anda tidak memiliki kewenangan penugasan untuk mengulas permohonan ini. Pastikan Anda telah ditugaskan secara resmi oleh koordinator.");
    }
}
