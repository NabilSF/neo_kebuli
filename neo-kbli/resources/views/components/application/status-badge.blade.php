@props(['status', 'size' => 'lg'])

@php
    use App\Enums\WorkflowState;
    
    $currentStatus = $status instanceof WorkflowState ? $status : WorkflowState::tryFrom($status ?? '');
    
    $statusColors = [
        WorkflowState::DRAFT->value => 'bg-slate-100 text-slate-800 border-border',
        WorkflowState::SUBMITTED->value => 'bg-info/10 text-info border-info/20',
        WorkflowState::UNDER_REVIEW->value => 'bg-warning/10 text-warning border-warning/20',
        WorkflowState::NEED_REVISION->value => 'bg-primary/10 text-primary border-primary/20',
        WorkflowState::APPROVED->value => 'bg-success/10 text-success border-success/20',
        WorkflowState::REJECTED->value => 'bg-danger/10 text-danger border-danger/20',
        WorkflowState::ARCHIVED->value => 'bg-slate-200 text-slate-700 border-slate-300',
    ];
    
    $val = $currentStatus?->value ?? (string)$status;
    $label = $currentStatus ? $currentStatus->label() : str_replace('_', ' ', $val);
    $color = $statusColors[$val] ?? $statusColors[WorkflowState::DRAFT->value];
    $sizeClasses = $size === 'sm' ? 'px-2 py-1 rounded-md text-xs' : 'px-4 py-2 rounded-xl text-sm';
@endphp
@endphp

<span class="inline-flex items-center font-bold uppercase tracking-wider border {{ $color }} {{ $sizeClasses }} shadow-sm">
    {{ $label }}
</span>
