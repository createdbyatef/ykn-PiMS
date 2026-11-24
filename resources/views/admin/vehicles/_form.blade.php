<div class="row g-3">
    <div class="col-12 col-md-6">
        <label for="plate_number" class="form-label fw-semibold small">No. Pendaftaran Kenderaan <span class="text-danger">*</span></label>
        <input
            type="text"
            name="plate_number"
            id="plate_number"
            value="{{ old('plate_number', $vehicle->plate_number ?? '') }}"
            class="form-control text-uppercase @error('plate_number') is-invalid @enderror"
            placeholder="Contoh: WYY 2048"
            required
        >
        @error('plate_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="model" class="form-label fw-semibold small">Model Kenderaan <span class="text-danger">*</span></label>
        <input
            type="text"
            name="model"
            id="model"
            value="{{ old('model', $vehicle->model ?? '') }}"
            class="form-control @error('model') is-invalid @enderror"
            placeholder="Contoh: Proton X70 1.8 TGDi"
            required
        >
        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="type" class="form-label fw-semibold small">Jenis Kenderaan <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach(\App\Models\Vehicle::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $vehicle->type ?? 'Sedan') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="capacity" class="form-label fw-semibold small">Kapasiti Penumpang <span class="text-danger">*</span></label>
        <input
            type="number"
            name="capacity"
            id="capacity"
            min="1"
            max="60"
            value="{{ old('capacity', $vehicle->capacity ?? 5) }}"
            class="form-control @error('capacity') is-invalid @enderror"
            required
        >
        @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="current_odometer" class="form-label fw-semibold small">Odometer Semasa (km) <span class="text-danger">*</span></label>
        <input
            type="number"
            name="current_odometer"
            id="current_odometer"
            min="0"
            value="{{ old('current_odometer', $vehicle->current_odometer ?? 0) }}"
            class="form-control @error('current_odometer') is-invalid @enderror"
            required
        >
        @error('current_odometer') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="status" class="form-label fw-semibold small">Status Kenderaan <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(\App\Models\Vehicle::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $vehicle->status ?? 'available') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="form-text">Kenderaan berstatus <strong>Penyelenggaraan (Maintenance)</strong> disekat secara automatik daripada kelulusan tempahan.</div>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
