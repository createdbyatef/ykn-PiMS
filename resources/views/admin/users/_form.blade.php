<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="name" class="form-label fw-semibold small">Nama Penuh <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="email" class="form-label fw-semibold small">Emel Rasmi YKN <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="role" class="form-label fw-semibold small">Peranan <span class="text-danger">*</span></label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach(\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}" @selected(old('role', $user->role ?? 'staff') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="jawatan" class="form-label fw-semibold small">Jawatan <span class="text-danger">*</span></label>
        <input type="text" name="jawatan" id="jawatan" value="{{ old('jawatan', $user->jawatan ?? '') }}" class="form-control @error('jawatan') is-invalid @enderror" placeholder="Contoh: Pilot HQ / Pegawai Tadbir" required>
        @error('jawatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="unit" class="form-label fw-semibold small">Unit / Bahagian <span class="text-danger">*</span></label>
        <input type="text" name="unit" id="unit" value="{{ old('unit', $user->unit ?? '') }}" class="form-control @error('unit') is-invalid @enderror" placeholder="Contoh: Unit Pentadbiran" required>
        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="phone" class="form-label fw-semibold small">No. Telefon</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Contoh: 019-2188401">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                @checked((bool) old('is_active', $user->is_active ?? true))>
            <label class="form-check-label fw-semibold small" for="is_active">Akaun Aktif</label>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <label for="password" class="form-label fw-semibold small">
            Kata Laluan @if(!isset($user)) <span class="text-danger">*</span> @else <span class="text-muted fw-normal">(Biarkan kosong jika tidak berubah)</span> @endif
        </label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" @required(!isset($user))>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="password_confirmation" class="form-label fw-semibold small">Sahkan Kata Laluan</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" @required(!isset($user))>
    </div>
</div>
