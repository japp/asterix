@csrf

<div class="row mb-3">
    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $target->name ?? '') }}" required autocomplete="name" autofocus>

        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="radeg" class="col-md-4 col-form-label text-md-end">{{ __('radeg') }}</label>

    <div class="col-md-6">
        <input id="radeg" type="text" class="form-control @error('radeg') is-invalid @enderror" name="radeg" value="{{ old('radeg', $target->radeg ?? '') }}" required autocomplete="radeg" autofocus>

        @error('radeg')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="decdeg" class="col-md-4 col-form-label text-md-end">{{ __('decdeg') }}</label>

    <div class="col-md-6">
        <input id="decdeg" type="text" class="form-control @error('decdeg') is-invalid @enderror" name="decdeg" value="{{ old('decdeg', $target->decdeg ?? '') }}" required autocomplete="decdeg" autofocus>

        @error('decdeg')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="vmag" class="col-md-4 col-form-label text-md-end">{{ __('V magnitude') }}</label>

    <div class="col-md-6">
        <input id="vmag" type="text" class="form-control @error('Vmag') is-invalid @enderror" name="vmag" value="{{ old('Vmag', $target->Vmag ?? '') }}" required autocomplete="Vmag" autofocus>
        @error('Vmag')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="row mb-0">
    <div class="col-md-6 offset-md-4">
        <button type="submit" class="btn btn-primary">
            {{ __('Save') }}
        </button>
    </div>
</div>