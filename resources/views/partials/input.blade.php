<input
    type="{{ $type }}"
    class="form-control @error($name) is-invalid @enderror"
    id="{{ $name }}"
    name="{{ $name }}"
    value="{{ old($name) }}"
>

@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

<label for="{{ $name }}" class="form-label">{{ $label }}</label>
