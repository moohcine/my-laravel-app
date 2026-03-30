@extends('layouts.app')

@section('title', __('Tasks – NDC PRO'))

@section('content')
<div class="container py-4 py-md-5">
    <div class="d-flex justify-content-start mb-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-link text-decoration-none p-0">
            <i class="bi bi-arrow-left"></i> {{ __('Back to dashboard') }}
        </a>
    </div>

    <div id="task-stats">
        @include('admin.tasks.partials.stats')
    </div>
    <div id="async-feedback" class="alert alert-success d-none mt-3" role="alert"></div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="small text-secondary text-uppercase mb-1">{{ __('Task management') }}</p>
            <h3 class="fw-bold mb-0 text-white">{{ __('Group tasks') }}</h3>
        </div>
    </div>

    <div class="ndc-card p-4 mb-4 task-form">
        <form method="POST" action="{{ route('admin.tasks.store') }}" class="row g-3" data-async="true">
            @csrf
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('Group') }}</label>
                <select name="group_id" class="form-select form-select-sm" required>
                    <option value="">{{ __('Select group') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('Title') }}</label>
                <input type="text" name="title" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('Description') }}</label>
                <input type="text" name="description" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-secondary">{{ __('Due date') }}</label>
                <input type="datetime-local" name="due_at" class="form-control form-control-sm">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn ndc-btn ndc-btn-primary btn-sm">
                    {{ __('Create task') }}
                </button>
            </div>
        </form>
    </div>

    <div id="task-stream">
        @include('admin.tasks.partials.stream', ['groups' => $groups])
    </div>
</div>
@endsection

@push('styles')
<style>
    .task-form {
        border-color: rgba(15, 23, 42, 0.08);
    }
    .task-card {
        border-radius: 1rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        padding: 1.25rem;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.1);
    }
    .task-status-badge {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        border-radius: 999px;
        padding: 0.2rem 0.9rem;
        font-weight: 700;
        border: 1px solid transparent;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .task-badge-completed {
        background: #0d5a2f;
        color: #fefefe;
        border-color: #769885;
        box-shadow: 0 12px 24px rgba(14, 68, 33, 0.25);
    }
    .task-badge-pending {
        background: #f8fafc;
        color: #1d4ed8;
        border-color: #bfdbfe;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }
    .stats-grid .stat-card {
        border-color: rgba(15, 23, 42, 0.12);
        min-height: 120px;
    }
    .task-statuses {
        min-height: 34px;
    }
    .task-card {
        border-radius: 1.5rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 45px rgba(15, 23, 42, 0.18);
    }
    .task-card header {
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        padding-bottom: 1rem;
    }
    .task-card footer {
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        padding-top: 1rem;
    }
    .completed-by {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.35rem;
    }
    .completed-by-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: #64748b;
    }
    .completed-by-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.85rem;
        border-radius: 999px;
        background: #0d5a2f;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1;
    }
    .completed-names {
        font-size: 0.95rem;
        color: #0f66d0;
        font-weight: 600;
        letter-spacing: 0.04em;
        border-bottom: 1px solid rgba(15, 23, 42, 0.12);
        padding-bottom: 0.35rem;
    }
    .task-edit-details summary {
        cursor: pointer;
        list-style: none;
    }
    .task-edit-details summary::-webkit-details-marker {
        display: none;
    }
    .task-edit-details[open] summary {
        color: #0d6efd;
    }
    .task-edit-panel {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        padding: 1rem;
        background: #f8fafc;
    }
    .task-edit-form .form-control {
        background: #fff;
    }
    .task-delete-form button {
        font-weight: 600;
    }
    .btn-custom-delete {
        border: 1px solid #dc2626;
        background: transparent;
        color: #dc2626;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-custom-delete:hover {
        background: #dc2626;
        color: #fff;
    }
    .btn-custom-edit {
        border: 1px solid #2563eb;
        background: transparent;
        color: #2563eb;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-custom-edit:hover {
        background: #2563eb;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    const statusPoll = () => {
        fetch("{{ route('admin.tasks.status') }}")
            .then(response => response.json())
            .then(tasks => {
                const completedLabel = "{{ __('Completed') }}";
                const notCompletedLabel = "{{ __('Not completed') }}";
                tasks.forEach(task => {
                    const card = document.querySelector(`.task-card[data-task-id="${task.id}"]`);
                    if (!card) return;
                    const statusBadge = card.querySelector('.task-status-badge');
                    const userContainer = card.querySelector('.task-statuses');
                    if (!statusBadge || !userContainer) return;
                if (task.statuses.some(s => s.status === 'completed')) {
                    statusBadge.classList.remove('bg-warning','text-warning');
                    statusBadge.classList.add('bg-success','text-success');
                    statusBadge.textContent = completedLabel;
                } else {
                    statusBadge.classList.add('bg-warning','text-warning');
                    statusBadge.classList.remove('bg-success','text-success');
                    statusBadge.textContent = notCompletedLabel;
                }
                    userContainer.innerHTML = '';
                    task.statuses.forEach(status => {
                        const badge = document.createElement('span');
                        badge.className = 'badge rounded-pill bg-info bg-opacity-20 text-info';
                        badge.textContent = `${status.user}: ${status.status === 'completed' ? completedLabel : notCompletedLabel}`;
                        userContainer.appendChild(badge);
                    });
                });
            });
    };

    const showFeedback = (message, type = 'success') => {
        const feedback = document.getElementById('async-feedback');
        if (!feedback) return;
        feedback.textContent = message;
        feedback.classList.remove('d-none', 'alert-success', 'alert-danger');
        feedback.classList.add(`alert-${type}`);
        setTimeout(() => {
            feedback.classList.add('d-none');
        }, 3000);
    };

    const updateTaskSections = ({ streamHtml, statsHtml }) => {
        if (statsHtml) {
            const statsContainer = document.getElementById('task-stats');
            if (statsContainer) {
                statsContainer.innerHTML = statsHtml;
            }
        }
        if (streamHtml) {
            const streamContainer = document.getElementById('task-stream');
            if (streamContainer) {
                streamContainer.innerHTML = streamHtml;
            }
        }
        attachAsyncForms(document.getElementById('task-stream'));
        initTaskActions(document.getElementById('task-stream'));
        statusPoll();
    };

    const attachAsyncForms = (scope = document) => {
        scope.querySelectorAll('form[data-async="true"]').forEach(form => {
            if (form.dataset.asyncBound === 'true') return;
            form.addEventListener('submit', async event => {
                event.preventDefault();
                await submitAsyncForm(form);
            });
            form.dataset.asyncBound = 'true';
        });
    };

    const submitAsyncForm = async form => {
        const submitButton = form.querySelector('[type="submit"]');
        const method = (form.method || 'GET').toUpperCase();
        submitButton?.setAttribute('disabled', 'disabled');
        try {
            const response = await fetch(form.action, {
                method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            if (!response.ok) {
                const errorPayload = await response.json().catch(() => null);
                showFeedback(errorPayload?.message || '{{ __('Operation failed') }}', 'danger');
                return;
            }

            const payload = await response.json();
            if (!payload.success) {
                showFeedback(payload.message || '{{ __('Operation failed') }}', 'danger');
                return;
            }
            showFeedback(payload.message || '{{ __('Saved successfully') }}');
            updateTaskSections(payload);
        } catch (error) {
            console.error(error);
            showFeedback('{{ __('Something went wrong') }}', 'danger');
        } finally {
            submitButton?.removeAttribute('disabled');
        }
    };

    const initTaskActions = (scope = document) => {
        scope.querySelectorAll('.btn-custom-edit').forEach(button => {
            if (button.dataset.actionsBound === 'true') return;
            button.addEventListener('click', () => {
                const panel = document.getElementById(`task-edit-${button.dataset.taskId}`);
                if (!panel) return;
                panel.hidden = false;
                panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
            button.dataset.actionsBound = 'true';
        });

        scope.querySelectorAll('.btn-cancel-edit').forEach(button => {
            if (button.dataset.actionsBound === 'true') return;
            button.addEventListener('click', () => {
                const panel = document.getElementById(`task-edit-${button.dataset.taskId}`);
                if (!panel) return;
                panel.hidden = true;
            });
            button.dataset.actionsBound = 'true';
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        statusPoll();
        setInterval(statusPoll, 4000);
        attachAsyncForms();
        initTaskActions();
    });
</script>
@endpush
