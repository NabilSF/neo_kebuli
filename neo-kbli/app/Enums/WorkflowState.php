<?php

namespace App\Enums;

enum WorkflowState: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case NEED_REVISION = 'need_revision';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case ARCHIVED = 'archived';

    /**
     * Dapatkan transisi status yang diizinkan untuk state ini.
     * Mengatur Flow Bisnis Aplikasi.
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DRAFT => [self::SUBMITTED],
            self::SUBMITTED => [self::UNDER_REVIEW, self::REJECTED],
            self::UNDER_REVIEW => [self::APPROVED, self::REJECTED, self::NEED_REVISION],
            self::NEED_REVISION => [self::SUBMITTED],
            self::APPROVED => [self::ARCHIVED],
            self::REJECTED => [self::ARCHIVED],
            self::ARCHIVED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Dapatkan label PUEBI untuk status.
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draf',
            self::SUBMITTED => 'Diajukan',
            self::UNDER_REVIEW => 'Sedang Ditinjau',
            self::NEED_REVISION => 'Perlu Revisi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    /**
     * Dapatkan varian warna UI (berdasarkan Theme Engine).
     */
    public function badgeVariant(): string
    {
        return match($this) {
            self::DRAFT, self::ARCHIVED => 'gray',
            self::SUBMITTED, self::UNDER_REVIEW => 'info',
            self::NEED_REVISION => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
