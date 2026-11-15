<div class="row">

    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __("Profile") }}</h6>

            </div>
            <!-- Card Body -->
            <div class="card-body">

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="name">{{ __("اسم المستخدم") }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" disabled>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="form-group">
                            <label for="email">{{ __("البريد اللاب") }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
                        </div>
                    </div>

                </div>
<div class="row">
    <div class="col-12">
                        <div class="form-group">
                            <label for="avatar">{{ __("Avatar") }}</label>
                            <div class="row col-lg-6 col-sm-12 mx-auto">
                                @forelse ($avatars as $avatar)
                                    <div class="col-6 col-md-3 mb-3">
                                        <label for="avatar-{{ $loop->index }}" class="d-flex flex-column align-items-center border rounded p-2 h-100">
                                            <input type="radio" class="form-check-input mb-2" id="avatar-{{ $loop->index }}" name="avatar" value="{{ $avatar }}" wire:model="selectedAvatar" {{ $selectedAvatar === $avatar ? 'checked' : '' }}>
                                            <img src="{{ asset('img/profiles/' . $avatar) }}" alt="{{ $avatar }}" class="img-thumbnail" style="width: 100%; max-width: 180px; height: 150px; object-fit: cover;">
                                            <span class="mt-2">{{ $avatar }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="col-12">{{ __("No profile images are available.") }}</p>
                                @endforelse
                            </div>
                            @if ($avatars)
                                <button type="button" class="btn btn-primary mt-2" wire:click="save">
                                    {{ __("Save avatar") }}
                                </button>
                            @endif
                            @if ($saved)
                                <div class="alert alert-success mt-3" role="status">
                                    {{ __("Avatar updated successfully.") }}
                                </div>
                            @endif
                        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>