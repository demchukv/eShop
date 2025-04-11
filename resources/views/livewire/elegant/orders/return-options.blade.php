<div class="container mt-4">
    <h2>Return Options</h2>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="saveReturnOption">
        <div class="mb-3">
            <label for="returnOption" class="form-label">Select Return Option</label>
            <select wire:model="selectedOption" class="form-select" id="returnOption">
                <option value="">Choose an option...</option>
                @foreach ($customCourierOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('selectedOption')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save Option</button>
        <a href="{{ route('orders.details', $orderItemId) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
