@php
    $bread_crumb['page_main_bread_crumb'] = labels('front_messages.returns_refunds', 'Returns/Refunds');
    $currency_symbol = $order_item->order['order_payment_currency_code'];
    $currency_details = fetchDetails('currencies', [
        'symbol' => $currency_symbol,
    ]);
@endphp
<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo />
    <div class="container-fluid h-100">
        <div class="row">
            <x-utility.my_account_slider.account_slider :$user_info />
            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                <div class="container mt-4">
                    <h2>Return Options</h2>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <!-- Інформація про товар -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    @if ($order_item->productVariant->image)
                                        <img src="{{ asset('storage/' . $order_item->productVariant->image) }}"
                                            alt="{{ $order_item->product->name ?? 'Product image' }}"
                                            style="width: 80px; height: 80px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        @if ($order_item->productVariant->product->image)
                                            <img src="{{ asset('storage/' . $order_item->productVariant->product->image) }}"
                                                alt="{{ $order_item->product->name ?? 'Product image' }}"
                                                style="width: 80px; height: 80px; object-fit: cover; margin-right: 10px;">
                                        @else
                                            <img src="{{ asset('path/to/default-image.jpg') }}"
                                                alt="Default product image"
                                                style="width: 80px; height: 80px; object-fit: cover; margin-right: 10px;">
                                        @endif
                                    @endif
                                </div>
                                <div class="col-md-10">
                                    <h5>{{ $order_item->productVariant->product->name }}</h5>
                                    <p><strong>{{ $currency_symbol . number_format($order_item->price, 2) }}</strong> x
                                        {{ $order_item->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Форма -->
                    <form wire:submit.prevent="saveReturnOption" enctype="multipart/form-data">
                        <!-- Delivery Status -->
                        <div class="mb-3">
                            <label for="deliveryStatus" class="form-label">Delivery Status</label>
                            <select wire:model.live="deliveryStatus" class="form-select" id="deliveryStatus">
                                <option value="">Choose an option...</option>
                                <option value="received">Item received in part or in full</option>
                                <option value="not_received">Package not received</option>
                            </select>
                            @error('deliveryStatus')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Додаткові поля для "Item received in part or in full" -->
                        @if ($deliveryStatus === 'received')
                            <!-- Reason -->
                            <div class="mb-3">
                                <label for="reason" class="form-label">Select the reason from the list below</label>
                                <select wire:model.live="reason" class="form-select" id="reason">
                                    <option value="">Select a reason...</option>
                                    @foreach (config('return_reasons') as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Application Type -->
                            @if ($reason)
                                <div class="mb-3">
                                    <label for="applicationType" class="form-label">Select your application type</label>
                                    <select wire:model.live="applicationType" class="form-select" id="applicationType">
                                        <option value="">Select an option...</option>
                                        @foreach (config('application_types') as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('applicationType')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Додаткові поля для Application Type -->
                            @if ($applicationType === 'return_and_refund' || $applicationType === 'refund_only')
                                <!-- Refund Amount -->
                                <div class="mb-3">
                                    <label for="refundAmount" class="form-label">Refund Amount</label>
                                    <input type="number" wire:model="refundAmount" class="form-control"
                                        id="refundAmount" step="0.01" min="0"
                                        max="{{ number_format($order_item->price * $order_item->quantity, 2) }}">
                                    <div><small>Enter an amount: min {{ $currency_symbol }}0.01 - max
                                            {{ $currency_symbol . number_format($order_item->price * $order_item->quantity, 2) }}</small>
                                    </div>
                                    @error('refundAmount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Refund Method -->
                                <div class="mb-3">
                                    <label for="refundMethod" class="form-label">Refund Method</label>
                                    <select wire:model="refundMethod" class="form-select" id="refundMethod">
                                        <option value="">Select a method...</option>
                                        @foreach (config('refund_methods') as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('refundMethod')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Please describe the issue and upload
                                        evidence of the proper disposal of the product.<br><small>Photos/videos and text
                                            description are required to upload to show item issue.</small></label>
                                    <textarea wire:model="description" class="form-control" id="description" rows="3"></textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Завантаження файлів із попереднім переглядом -->
                                <div class="mb-3">
                                    <label for="upload" class="form-label">Upload Photo/Video</label>
                                    <input type="file" wire:model="newUploads" class="form-control" id="upload"
                                        multiple accept="image/*,video/*">
                                    @error('newUploads.*')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <!-- Контейнер для попереднього перегляду -->
                                    <div class="preview-container mt-3"
                                        style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach ($tempUploads as $key => $file)
                                            <div class="preview-item"
                                                style="position: relative; width: 100px; height: 100px;">
                                                @if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif']))
                                                    <img src="{{ $file->temporaryUrl() }}" alt="Preview"
                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <video controls
                                                        style="width: 100%; height: 100%; border-radius: 5px;">
                                                        <source src="{{ $file->temporaryUrl() }}"
                                                            type="{{ $file->getMimeType() }}">
                                                    </video>
                                                @endif
                                                <button type="button" wire:click="removeFile('{{ $key }}')"
                                                    style="position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer;">
                                                    ×
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Return Method (тільки для Return and Refund) -->
                                @if ($applicationType === 'return_and_refund')
                                    <div class="mb-3">
                                        <label class="form-label">Return Method</label>
                                        <h3 class="mb-0">Drop-off</h3>
                                        <p>Head to the post office to send your package.</p>
                                    </div>
                                @endif
                            @endif
                        @endif

                        @if ($errors->has('form'))
                            <div class="alert alert-danger">
                                {{ $errors->first('form') }}
                            </div>
                        @endif
                        <!-- Кнопка Submit -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('orders.details', $order_item->order_id) }}"
                            class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .preview-item img,
        .preview-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item button {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 16px;
            line-height: 24px;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('livewire:updated', () => {
            console.log('Livewire updated');
            const input = document.querySelector('#upload');
            if (input) {
                console.log('Input cleared'); // Лог для консолі
                input.value = null; // Очистити input
            } else {
                console.error('Input #upload not found');
            }
        });
    </script>
@endpush
