@foreach($groups as $group)
    <section class="ndc-card p-4 mb-3">
        <header class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-3">
            <div>
                <p class="small text-uppercase text-muted mb-1">{{ __('Group') }}</p>
                <h5 class="mb-0">{{ $group->name }}</h5>
            </div>
            <div class="text-end">
                <p class="small text-muted mb-0">{{ __('Interns') }}</p>
                <strong class="fs-4">{{ $group->interns->count() }}</strong>
            </div>
        </header>
        @forelse($group->tasks as $task)
            @php $badgeStatus = $task->status === 'completed'; @endphp
            <article class="task-card mb-3" data-task-id="{{ $task->id }}">
                <div class="d-flex justify-content-between flex-wrap gap-3 align-items-start mb-2">
                    <div>
                        <h6 class="text-dark fw-semibold mb-1 text-capitalize">{{ $task->title }}</h6>
                        <p class="text-muted small mb-1">{{ $task->description }}</p>
                        @if($task->due_at)
                            <p class="mb-0 text-muted small">
                                {{ __('Due date') }}:
                                <time datetime="{{ $task->due_at->format('c') }}">{{ $task->due_at->format('d M Y H:i') }}</time>
                            </p>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="task-status-badge badge {{ $badgeStatus ? 'task-badge-completed' : 'task-badge-pending' }}">
                            {{ $badgeStatus ? __('Completed') : __('Not completed') }}
                        </span>
                        <p class="small text-muted mb-0">{{ __('Created on') }} {{ $task->created_at->format('d M') }}</p>
                    </div>
                </div>
                @php
                    $completedNames = $task->statuses
                        ->where('status', 'completed')
                        ->pluck('user.name');
                @endphp
                @if($completedNames->isNotEmpty())
                    <div class="completed-by mb-3">
                        <span class="completed-by-label">{{ __('Completed by') }}</span>
                        <span class="completed-by-chip">
                            {{ $completedNames->implode(', ') }}
                        </span>
                    </div>
                @endif
                <footer class="task-actions d-flex flex-wrap gap-2 align-items-center">
                    <button type="button" class="btn btn-custom-edit" data-task-id="{{ $task->id }}">
                        {{ __('Edit task') }}
                    </button>
                    <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" class="task-delete-form" data-async="true">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-custom-delete" type="submit">{{ __('Delete task') }}</button>
                    </form>
                </footer>
                <div class="task-edit-panel mt-3" id="task-edit-{{ $task->id }}" hidden>
                    <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="row g-3 task-edit-form" data-async="true">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $task->title) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">{{ __('Description') }}</label>
                            <input type="text" name="description" class="form-control form-control-sm" value="{{ old('description', $task->description) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">{{ __('Due date') }}</label>
                            <input type="datetime-local" name="due_at" class="form-control form-control-sm" value="{{ old('due_at', optional($task->due_at)->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                            <button class="btn btn-sm btn-primary" type="submit">{{ __('Save changes') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-edit" data-task-id="{{ $task->id }}">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <p class="small text-secondary mb-0">{{ __('No tasks yet for this group.') }}</p>
        @endforelse
    </section>
@endforeach
