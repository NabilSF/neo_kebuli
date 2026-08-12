<?php

namespace App\Support\UI;

class Theme
{
    public static function buttonSize(string $size, bool $iconOnly = false): string
    {
        $sizes = [
            'xs' => 'text-xs px-2.5 py-1.5 min-h-[32px]',
            'sm' => 'text-sm px-3 py-2 min-h-[36px]',
            'md' => 'text-sm px-4 py-2 min-h-[44px]',
            'lg' => 'text-base px-6 py-3 min-h-[48px]',
            'xl' => 'text-base px-8 py-4 min-h-[56px]',
        ];

        if ($iconOnly) {
            $sizes = [
                'xs' => 'text-xs p-1.5 min-h-[32px] min-w-[32px]',
                'sm' => 'text-sm p-2 min-h-[36px] min-w-[36px]',
                'md' => 'text-sm p-2.5 min-h-[44px] min-w-[44px]',
                'lg' => 'text-base p-3 min-h-[48px] min-w-[48px]',
                'xl' => 'text-base p-4 min-h-[56px] min-w-[56px]',
            ];
        }

        return $sizes[$size] ?? $sizes['md'];
    }

    public static function buttonVariant(string $variant): string
    {
        $variants = [
            'primary' => 'bg-accent text-white hover:bg-accent-hover active:bg-accent-active',
            'secondary' => 'bg-surface border border-border text-primary hover:bg-secondary',
            'ghost' => 'bg-transparent text-primary hover:bg-secondary',
            'outline' => 'bg-transparent border border-border text-primary hover:bg-secondary',
            'danger' => 'bg-danger text-white hover:opacity-90',
            'success' => 'bg-success text-white hover:opacity-90',
            'warning' => 'bg-warning text-white hover:opacity-90',
            'info' => 'bg-info text-white hover:opacity-90',
        ];

        return $variants[$variant] ?? $variants['primary'];
    }

    public static function badgeVariant(string $variant): string
    {
        $variants = [
            'primary' => 'bg-primary/10 text-primary',
            'success' => 'bg-success/10 text-success',
            'warning' => 'bg-warning/10 text-warning',
            'danger' => 'bg-danger/10 text-danger',
            'info' => 'bg-info/10 text-info',
            'neutral' => 'bg-slate-100 text-slate-800',
            'outline' => 'bg-transparent border border-border text-slate-800',
        ];

        return $variants[$variant] ?? $variants['neutral'];
    }

    public static function badgeDot(string $variant): string
    {
        $dots = [
            'primary' => 'bg-primary',
            'success' => 'bg-success',
            'warning' => 'bg-warning',
            'danger' => 'bg-danger',
            'info' => 'bg-info',
            'neutral' => 'bg-slate-500',
            'outline' => 'bg-slate-500',
        ];

        return $dots[$variant] ?? $dots['neutral'];
    }

    public static function alertVariant(string $variant): string
    {
        $variants = [
            'info' => 'bg-info/10 border-info/20 text-info-dark',
            'success' => 'bg-success/10 border-success/20 text-success-dark',
            'warning' => 'bg-warning/10 border-warning/20 text-warning-dark',
            'error' => 'bg-danger/10 border-danger/20 text-danger-dark',
            'neutral' => 'bg-slate-100 border-slate-200 text-slate-800',
        ];

        return $variants[$variant] ?? $variants['neutral'];
    }

    public static function spinnerSize(string $size): string
    {
        $sizes = [
            'xs' => 'h-3 w-3',
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-8 w-8',
            'xl' => 'h-12 w-12',
        ];

        return $sizes[$size] ?? $sizes['md'];
    }

    public static function statusIndicatorVariant(string $status): string
    {
        $statuses = [
            'aktif' => 'bg-success-light text-success',
            'nonaktif' => 'bg-slate-100 text-slate-600',
            'diproses' => 'bg-info-light text-info',
            'menunggu' => 'bg-warning-light text-warning',
            'disetujui' => 'bg-success-light text-success',
            'ditolak' => 'bg-danger-light text-danger',
            'diarsipkan' => 'bg-slate-200 text-slate-700',
        ];

        return $statuses[strtolower($status)] ?? $statuses['nonaktif'];
    }
}
