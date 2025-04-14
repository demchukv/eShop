<div id="page-content">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h2 class="mb-0">Write a review</h2>
                    </div>
                    <div class="card-body">
                        @if ($orderItems->isEmpty())
                            <p>There are no products to view or review.</p>
                        @else
                            <form wire:submit.prevent="save_review" class="product-review-form new-review-form"
                                enctype="multipart/form-data" wire:ignore>
                                @foreach ($orderItems as $item)
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <!-- Зображення продукту -->
                                            @if ($item->productVariant->product->image)
                                                <img src="{{ asset('storage/' . $item->productVariant->product->image) }}"
                                                    alt="{{ $item->product->name ?? 'Product image' }}"
                                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                            @else
                                                <img src="{{ asset('path/to/default-image.jpg') }}"
                                                    alt="Default product image"
                                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                            @endif
                                            <!-- Назва продукту як посилання -->
                                            <h5>
                                                <a href="{{ route('products.details', $item->productVariant->product->slug ?? $item->productVariant->product->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $item->product->name ?? 'Product name' }}
                                                </a>
                                                @if ($item->variant_name)
                                                    - {{ $item->variant_name }}
                                                @endif
                                            </h5>
                                        </div>
                                        @if ($item->is_completed == 1)
                                            @php
                                                $review = \App\Models\ProductRating::where('user_id', Auth::id())
                                                    ->where('product_id', $item->product_id)
                                                    ->first();
                                            @endphp
                                            @if ($item->is_write_review == 0)
                                                <fieldset class="row spr-form-contact">
                                                    <div class="col-sm-6 spr-form-review-rating form-group">
                                                        <label
                                                            class="spr-form-label">{{ labels('front_messages.rating', 'Rating') }}</label>
                                                        <div class="product-review pt-1">
                                                            <div class="review-rating">
                                                                <input id="rating-{{ $item->id }}"
                                                                    name="ratings[{{ $item->id }}]"
                                                                    class="kv-ltr-theme-svg-star star-rating rating-loading review_rating"
                                                                    value=""
                                                                    wire:model="ratings.{{ $item->id }}"
                                                                    dir="ltr" data-size="s"
                                                                    data-show-clear="false" data-show-caption="false"
                                                                    data-step="1">
                                                            </div>
                                                            @error('ratings.' . $item->id)
                                                                <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 spr-form-review-body form-group">
                                                        <label class="spr-form-label"
                                                            for="add_image_{{ $item->id }}">
                                                            {{ labels('front_messages.add_image_or_video', 'Add Image or Video') }}
                                                        </label>
                                                        <input id="review_image_{{ $item->id }}" type="file"
                                                            name="images[{{ $item->id }}][]" multiple
                                                            accept="image/gif, image/jpeg, image/png">
                                                        @error('images.' . $item->id)
                                                            <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 spr-form-review-body form-group">
                                                        <label class="spr-form-label"
                                                            for="message_{{ $item->id }}">
                                                            {{ labels('front_messages.description', 'Description') }}
                                                        </label>
                                                        <div class="spr-form-input">
                                                            <textarea wire:model="comments.{{ $item->id }}" class="spr-form-input spr-form-input-textarea"
                                                                id="message_{{ $item->id }}" name="comments[{{ $item->id }}]" rows="3"></textarea>
                                                            @error('comments.' . $item->id)
                                                                <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 spr-form-review-body form-group">
                                                        <label class="spr-form-label"
                                                            for="advantages_{{ $item->id }}">
                                                            {{ labels('front_messages.advantages', 'Advantages') }}
                                                            (optional)
                                                        </label>
                                                        <div class="spr-form-input">
                                                            <textarea wire:model="advantages.{{ $item->id }}" class="spr-form-input spr-form-input-textarea"
                                                                id="advantages_{{ $item->id }}" name="advantages[{{ $item->id }}]" rows="3"></textarea>
                                                            @error('advantages.' . $item->id)
                                                                <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 spr-form-review-body form-group">
                                                        <label class="spr-form-label"
                                                            for="disadvantages_{{ $item->id }}">
                                                            {{ labels('front_messages.disadvantages', 'Disadvantages') }}
                                                            (optional)
                                                        </label>
                                                        <div class="spr-form-input">
                                                            <textarea wire:model="disadvantages.{{ $item->id }}" class="spr-form-input spr-form-input-textarea"
                                                                id="disadvantages_{{ $item->id }}" name="disadvantages[{{ $item->id }}]" rows="3"></textarea>
                                                            @error('disadvantages.' . $item->id)
                                                                <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            @else
                                                @if ($review)
                                                    <div class="review-display">
                                                        <p><strong>Rating:</strong> {{ $review->rating }}/5</p>
                                                        <p><strong>Comment:</strong> {{ $review->comment }}</p>
                                                        @if ($review->advantages)
                                                            <p><strong>Advantages:</strong> {{ $review->advantages }}
                                                            </p>
                                                        @endif
                                                        @if ($review->disadvantages)
                                                            <p><strong>Disadvantages:</strong>
                                                                {{ $review->disadvantages }}</p>
                                                        @endif
                                                        @if ($review->images)
                                                            @foreach (json_decode($review->images) as $image)
                                                                <img src="{{ asset('storage/' . $image) }}"
                                                                    alt="Review image" style="max-width: 100px;" />
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @else
                                                    <p>The review has been saved.</p>
                                                @endif
                                            @endif
                                        @else
                                            <p>This item has not yet been confirmed as received.</p>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="spr-form-actions clearfix">
                                    <input type="submit" class="btn btn-primary spr-button spr-button-primary"
                                        value="Submit Reviews" />
                                </div>
                            </form>
                        @endif
                        @if (session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif
                        @error('general')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script>
        // Функція для ініціалізації FilePond для всіх полів
        function initializeFilePonds() {
            const inputs = document.querySelectorAll('input[id^="review_image_"]');
            inputs.forEach(input => {
                const orderItemId = input.id.replace('review_image_', '');
                const pond = FilePond.create(input, {
                    allowMultiple: true,
                    acceptedFileTypes: ['image/gif', 'image/jpeg', 'image/png'],
                    maxFileSize: '2MB',
                    server: {
                        process: (fieldName, file, metadata, load, error, progress, abort) => {
                            const formData = new FormData();
                            formData.append('file', file, file.name);

                            fetch('/upload-temp', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                    },
                                    body: formData,
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.path) {
                                        load(data.path);
                                        @this.call('addImage', data.path, orderItemId);
                                    } else {
                                        error('Upload failed');
                                    }
                                })
                                .catch(err => {
                                    console.error('FilePond process error:', err);
                                    error('Request failed');
                                });
                        },
                        revert: (uniqueFileId, load, error) => {
                            fetch('/delete-temp', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                    },
                                    body: JSON.stringify({
                                        path: uniqueFileId
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        @this.call('removeImage', uniqueFileId, orderItemId);
                                        load();
                                    } else {
                                        error('Delete failed');
                                    }
                                })
                                .catch(err => {
                                    console.error('FilePond revert error:', err);
                                    error('Request failed');
                                });
                        },
                    },
                });

                window.addEventListener('showSuccess', () => {
                    pond.removeFiles();
                });
            });
        }

        // Виконуємо ініціалізацію після завантаження всіх скриптів
        document.addEventListener('DOMContentLoaded', () => {
            if (window.isScriptsInitialized) {
                initializeFilePonds();
            } else {
                window.addEventListener('scripts:loaded', () => {
                    initializeFilePonds();
                });
            }
        });
    </script>
@endpush
