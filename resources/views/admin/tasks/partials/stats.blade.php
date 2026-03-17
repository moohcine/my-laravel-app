<div class="row g-3 mb-4 stats-grid">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card ndc-card p-3 h-100">
            <p class="text-muted small mb-0">{{ __('Total tasks') }}</p>
            <h4 class="fw-bold mt-1">{{ $statistics['total_tasks'] }}</h4>
            <p class="small text-muted mb-0">{{ __('Created across the department') }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card ndc-card p-3 h-100">
            <p class="text-muted small mb-0">{{ __('Completed') }}</p>
            <h4 class="fw-bold text-success mt-1">{{ $statistics['completed_tasks'] }}</h4>
            <p class="small text-muted mb-0">{{ __('Marked done by interns') }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card ndc-card p-3 h-100">
            <p class="text-muted small mb-0">{{ __('In progress') }}</p>
            <h4 class="fw-bold text-warning mt-1">{{ $statistics['pending_tasks'] }}</h4>
            <p class="small text-muted mb-0">{{ __('Awaiting completion') }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card ndc-card p-3 h-100">
            <p class="text-muted small mb-0">{{ __('Groups monitored') }}</p>
            <h4 class="fw-bold mt-1">{{ $statistics['groups_managed'] }}</h4>
            <p class="small text-muted mb-0">{{ __('Each with dedicated tasks') }}</p>
        </div>
    </div>
</div>
