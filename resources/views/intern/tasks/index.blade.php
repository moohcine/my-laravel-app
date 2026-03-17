@extends('layouts.app')

@section('title', __('My tasks – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="small text-secondary text-uppercase mb-1">{{ __('Group tasks') }}</p>
            <h3 class="fw-bold text-white mb-0">{{ __('Your assignments') }}</h3>
        </div>
    </div>

    <div class="row g-3">
        @foreach($taskAssignments as $task)
            <div class="col-md-6">
                <div class="ndc-card p-4 task-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="text-white mb-1">{{ $task->title }}</h5>
                            <p class="small text-secondary mb-0">{{ $task->description }}</p>
                            @if ($task->due_at)
                                <p class="small text-info mb-1">{{ __('Due date') }}: {{ $task->due_at->format('d M Y H:i') }}</p>
                            @endif
                            <div class="small text-secondary mt-1">{{ $task->group->name }}</div>
                        </div>
                        @php $isCompleted = ($statusMap[$task->id] ?? 'not_completed') === 'completed'; @endphp
                        <span class="badge rounded-pill {{ $isCompleted ? 'badge-completed bg-success bg-opacity-70 border border-success border-opacity-80' : 'bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50' }}">
                            {{ $isCompleted ? __('Completed') : __('Not completed') }}
                        </span>
                    </div>
                    @php $isCompleted = ($statusMap[$task->id] ?? 'not_completed') === 'completed'; @endphp
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button type="button" class="btn btn-view-details btn-sm" data-task-id="{{ $task->id }}">
                            {{ __('View details') }}
                        </button>
                        <form method="POST" action="{{ route('intern.tasks.status', $task) }}" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="status" value="{{ $isCompleted ? 'not_completed' : 'completed' }}">
                            <button type="submit" class="btn ndc-btn ndc-btn-outline btn-sm w-100 {{ $isCompleted ? 'btn-mark-incomplete' : 'btn-mark-complete' }}">
                                {{ $isCompleted ? __('Mark not completed') : __('Mark completed') }}
                            </button>
                        </form>
                    </div>
                    <div class="task-detail-panel mt-3" id="task-detail-{{ $task->id }}" hidden>
                        <p class="small mb-1 text-dark">{{ __('Group') }}: <strong>{{ $task->group->name }}</strong></p>
                        <p class="small mb-1 text-dark">{{ __('Status') }}: <strong>{{ ($statusMap[$task->id] ?? 'not_completed') === 'completed' ? __('Completed') : __('Not completed') }}</strong></p>
                        @if ($task->due_at)
                            <p class="small mb-1 text-dark">{{ __('Due date') }}: <strong>{{ $task->due_at->format('d M Y H:i') }}</strong></p>
                        @endif
                        <p class="small mb-0 text-dark">{{ __('Created') }} {{ $task->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        @endforeach
        @if($taskAssignments->isEmpty())
            <div class="col-12">
                <div class="ndc-card p-4 text-center text-secondary">{{ __('No tasks assigned yet.') }}</div>
            </div>
        @endif
</div>
</div>
@endsection

@push('styles')
<style>
    .task-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 15px 26px rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .task-card .badge {
        font-size: 0.75rem;
        letter-spacing: 0.04em;
    }

    .task-actions .btn {
        border-radius: 0.85rem;
        min-width: 160px;
        padding: 0.65rem 1.6rem;
        border-width: 1px;
        background: #e0f2fe;
        color: #0f172a;
        border-color: rgba(59, 130, 246, 0.6);
        transition: all 0.18s ease;
    }

    .task-actions .btn:hover {
        background: #7dd3fc;
        color: #0f172a;
        border-color: rgba(59, 130, 246, 1);
    }

    .btn-view-details {
        border: 1px solid rgba(59, 130, 246, 0.5);
        background: rgba(59, 130, 246, 0.08);
        color: #1347a1;
        font-weight: 600;
        padding: 0.55rem 1.4rem;
        border-radius: 999px;
        letter-spacing: 0.04em;
        transition: all 0.2s ease;
    }

    .btn-view-details:hover {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .btn-mark-incomplete {
        border-color: #dc2626;
        color: #dc2626;
    }

    .btn-mark-incomplete:hover,
    .btn-mark-incomplete:focus {
        background: #dc2626;
        color: #fff;
        border-color: #dc2626;
    }

    .btn-mark-complete,
    .btn-mark-incomplete {
        color: #0f172a;
    }

    .btn-mark-complete:hover,
    .btn-mark-complete:focus {
        background: #16a34a;
        color: #fff;
        border-color: #16a34a;
    }

    .task-detail-panel {
        background: #fff;
        border-radius: 0.75rem;
        border: 1px solid rgba(15, 15, 15, 0.35);
        padding: 1rem;
        margin-bottom: 0.5rem;
        color: #0f172a;
        box-shadow: 0 8px 20px rgba(15, 15, 15, 0.12);
    }
    .task-detail-panel p {
        margin-bottom: 0.3rem;
    }
    .badge-completed {
        color: #fff !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
    }

    .task-detail-panel p {
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-view-details').forEach(button => {
            button.addEventListener('click', () => {
                const panel = document.getElementById(`task-detail-${button.dataset.taskId}`);
                if (!panel) return;
                panel.hidden = !panel.hidden;
                panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    });
</script>
@endpush
