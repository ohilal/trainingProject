<div class="card shadow mb-4 border-bottom-success" style="height: 100%;">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold ">
        <i class="fas fa-award"></i> {{ __('شارة التميز') }}
    </h6></div>
    <div class="card-body">
        <div class="d-flex flex-column align-items-center text-center" >
            <img
                src="{{ $badgeUrl }}"
                alt="{{ $badgeTitle }}"
                class="img-fluid"
                style="max-width: 150px;"
            >
            <div class="mt-2 font-weight-bold p">{{ $badgeTitle }}</div>
        </div>
    </div>
</div>