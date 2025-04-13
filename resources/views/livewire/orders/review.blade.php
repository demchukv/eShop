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
                            @foreach ($orderItems as $item)
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <!-- Додаємо зображення продукту -->
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
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 mb-4" wire:ignore>
                                                <form wire:submit.prevent="save_review({{ $item->id }})"
                                                    class="product-review-form new-review-form"
                                                    enctype="multipart/form-data">
                                                    <fieldset class="row spr-form-contact">
                                                        <div class="col-sm-6 spr-form-review-rating form-group">
                                                            <input type="hidden" name="id" value="">
                                                            <label
                                                                class="spr-form-label">{{ labels('front_messages.rating', 'Rating') }}</label>
                                                            <div class="product-review pt-1">
                                                                <div class="review-rating">
                                                                    <input id="rating" name="input-3-ltr-star-md"
                                                                        class="kv-ltr-theme-svg-star star-rating rating-loading review_rating"
                                                                        value="" wire:model="rating"
                                                                        dir="ltr" data-size="s"
                                                                        data-show-clear="false"
                                                                        data-show-caption="false" data-step="1"
                                                                        wire:key>
                                                                </div>
                                                                @error('rating')
                                                                    <p class="fw-400 text-danger mt-1">{{ $message }}
                                                                    </p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-12 spr-form-review-body form-group">
                                                            <label class="spr-form-label" for="add_image">
                                                                {{ labels('front_messages.add_image_or_video', 'Add Image or Video') }}
                                                            </label>
                                                            <input id="review_image" type="file" name="image[]"
                                                                multiple accept="image/gif, image/jpeg, image/png">
                                                        </div>
                                                        @error('images')
                                                            <p class="fw-400 text-danger mt-1"></p>
                                                        @enderror
                                                        <div class="col-12 spr-form-review-body form-group">
                                                            <label class="spr-form-label" for="message">
                                                                {{ labels('front_messages.description', 'Description') }}
                                                            </label>
                                                            <div class="spr-form-input">
                                                                <textarea wire:model="comment" class="spr-form-input spr-form-input-textarea" id="message" name="message"
                                                                    rows="3"></textarea>
                                                                @error('comment')
                                                                    <p class="fw-400 text-danger mt-1">{{ $message }}
                                                                    </p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-12 spr-form-review-body form-group">
                                                            <label class="spr-form-label" for="advantages">
                                                                {{ labels('front_messages.advantages', 'Advantages') }}
                                                                (optional)
                                                            </label>
                                                            <div class="spr-form-input">
                                                                <textarea wire:model="advantages" class="spr-form-input spr-form-input-textarea" id="advantages" name="advantages"
                                                                    rows="3"></textarea>
                                                                @error('advantages')
                                                                    <p class="fw-400 text-danger mt-1">{{ $message }}
                                                                    </p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-12 spr-form-review-body form-group">
                                                            <label class="spr-form-label" for="disadvantages">
                                                                {{ labels('front_messages.disadvantages', 'Disadvantages') }}
                                                                (optional)
                                                            </label>
                                                            <div class="spr-form-input">
                                                                <textarea wire:model="disadvantages" class="spr-form-input spr-form-input-textarea" id="disadvantages"
                                                                    name="disadvantages" rows="3"></textarea>
                                                                @error('disadvantages')
                                                                    <p class="fw-400 text-danger mt-1">{{ $message }}
                                                                    </p>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="spr-form-actions clearfix">
                                                        <input type="submit"
                                                            class="btn btn-primary spr-button spr-button-primary"
                                                            value="Submit Review" />
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            @if ($review)
                                                <div class="review-display">
                                                    <p><strong>Rating:</strong> {{ $review->rating }}/5</p>
                                                    <p><strong>Comment:</strong> {{ $review->comment }}</p>
                                                    @if ($review->advantages)
                                                        <p><strong>Advantages:</strong> {{ $review->advantages }}</p>
                                                    @endif
                                                    @if ($review->disadvantages)
                                                        <p><strong>Disadvantages:</strong> {{ $review->disadvantages }}
                                                        </p>
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
        // Функція для ініціалізації FilePond
        function initializeFilePond() {
            const inputElement = document.querySelector('#review_image');
            if (inputElement) {
                const pond = FilePond.create(inputElement, {
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
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content,
                                    },
                                    body: formData,
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.path) {
                                        load(data.path);
                                        @this.call('addImage', data.path);
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
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content,
                                    },
                                    body: JSON.stringify({
                                        path: uniqueFileId
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        @this.call('removeImage', uniqueFileId);
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
            } else {
                console.error('FilePond input (#review_image) not found');
            }
        }

        // Виконуємо ініціалізацію після завантаження всіх скриптів
        document.addEventListener('DOMContentLoaded', () => {
            if (window.isScriptsInitialized) {
                initializeFilePond();
            } else {
                window.addEventListener('scripts:loaded', () => {
                    initializeFilePond();
                });
            }
        });
    </script>
@endpush
