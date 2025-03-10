<div>
    <!-- Password Field -->
    <div class="mb-3 form-password-toggle">
        <label class="form-label" for="password">
            {{ labels('admin_labels.password', 'Password') }}
            <span class="text-asterisks text-sm">*</span>
        </label>
        <div class="input-group">
            <input wire:model="password" type="{{ $showPassword ? 'text' : 'password' }}"
                class="form-control show_seller_password" name="password" id="password" placeholder="Enter Your Password"
                autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" wire:click="togglePasswordVisibility('password')">
                <i class="bi {{ $showPassword ? 'bi-eye-slash' : 'bi-eye' }}"></i>
            </button>
        </div>
        @error('password')
            <span class="error text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Confirm Password Field -->
    <div class="mb-3 form-password-toggle">
        <label class="form-label" for="confirm_password">
            {{ labels('admin_labels.confirm_password', 'Confirm Password') }}
            <span class="text-asterisks text-sm">*</span>
        </label>
        <div class="input-group">
            <input wire:model="confirm_password" type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Your Password"
                autocomplete="off">
            <button class="btn btn-outline-secondary" type="button"
                wire:click="togglePasswordVisibility('confirm_password')">
                <i class="bi {{ $showConfirmPassword ? 'bi-eye-slash' : 'bi-eye' }}"></i>
            </button>
        </div>
        @error('confirm_password')
            <span class="error text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
