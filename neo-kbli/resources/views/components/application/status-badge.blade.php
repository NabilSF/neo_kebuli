@props(['status', 'size' => 'lg'])

@php
    use App\Enums\ApplicationStatus;
    $statusColors = [
        ApplicationStatus::DRAFT->value => 'bg-gray-100 text-gray-800 border-gray-200',
        ApplicationStatus::SUBMITTED->value => 'bg-blue-100 text-blue-800 border-blue-200',
        ApplicationStatus::UNDER_REVIEW->value => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        ApplicationStatus::NEED_REVISION->value => 'bg-orange-100 text-orange-800 border-orange-200',
        ApplicationStatus::APPROVED->value => 'bg-green-100 text-green-800 border-green-200',
        ApplicationStatus::REJECTED->value => 'bg-red-100 text-red-800 border-red-200',
    ];
    // Handle both Enum instance and string values
    $currentStatus = $status instanceof ApplicationStatus ? $status->value : $status;
    $statusLabels = [
        ApplicationStatus::DRAFT->value => 'Draf',
        ApplicationStatus::SUBMITTED->value => 'Diajukan',
        ApplicationStatus::UNDER_REVIEW->value => 'Dalam Peninjauan',
        ApplicationStatus::NEED_REVISION->value => 'Perlu Revisi',
        ApplicationStatus::APPROVED->value => 'Disetujui',
        ApplicationStatus::REJECTED->value => 'Ditolak',
    ];
    $label = $statusLabels[$currentStatus] ?? str_replace('_', ' ', $currentStatus);
    $color = $statusColors[$currentStatus] ?? $statusColors[ApplicationStatus::DRAFT->value];
    $sizeClasses = $size === 'sm' ? 'px-2 py-1 rounded-md text-xs' : 'px-4 py-2 rounded-xl text-sm';
@endphp

<span class="inline-flex items-center font-bold uppercase tracking-wider border {{ $color }} {{ $sizeClasses }} shadow-sm">
    {{ $label }}
</span>
