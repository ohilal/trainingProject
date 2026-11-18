<div class="card shadow mb-4 border-bottom-info">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold ">
        <i class="fas fa-award"></i> {{ __('شارة التميز') }}
    </h6></div>
    <div class="card-body">
        <div class="d-flex flex-column align-items-center text-center" style="height: 100%;">
            <img
                src="{{ $badgeUrl }}"
                alt="{{ $badgeTitle }}"
                class="img-fluid"
                style="max-width: 120px;"
            >
            <div class="mt-2 font-weight-bold">{{ $badgeTitle }}</div>
        </div>
    </div>
</div>