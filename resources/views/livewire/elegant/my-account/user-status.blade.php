@props(['user_info'])
<?php
$bread_crumb['page_main_bread_crumb'] = labels('front_messages.addresses', 'Addresses');

?>

<div>
<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <div class="row">
            <x-utility.my_account_slider.account_slider :$user_info />
            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                <div class="dashboard-conten h-100">
                    <!-- User status -->
                    <div class="h-100" id="user-status">
                        <div class="account-info h-100">
                            <div class="welcome-msg mb-4">
                                <h2 class="mb-0">{{ labels('front_messages.user_status', 'User Status') }}</h2>
                                <p>{{ labels('front_messages.user_status_info', 'Your current status: ') }}{{ $user_info->role->description }}
                                </p>
                                @if(!$user_status && ($user_info->role_id == 2 || $user_info->role_id == 7))
                                <p>{{ labels('front_messages.user_status_change', 'You can submit a request to change your status.') }}
                                </p>
                                @endif
                            </div>

                            @if (!$user_status && ($user_info->role_id == 2 || $user_info->role_id == 7))
                            <div class="row">
                                    @if ($user_info->role_id == 2)
                                <div class="col-xs-12 col-md-6">
                                    <button wire:ignore type="button" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#dealerModal">
                                        {{ labels('front_messages.become_dealer', 'Become a dealer') }}</button>
                                </div>
                                    @endif
                                    @if ($user_info->role_id == 2 || $user_info->role_id == 7)
                                <div class="col-xs-12 col-md-6">
                                    <button wire:ignore type="button" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#managerModal">
                                        {{ labels('front_messages.become_manager', 'Become a manager') }}</button>
                                </div>
                                    @endif
                            </div>

                            @if (session('success'))
                            <span class="text-green-500">{{ session('success') }}</span>
                            @endif

                            {{-- Dealer Modal --}}
                            <div wire:ignore.self class="modal fade" id="dealerModal" tabindex="-1"
                                aria-labelledby="dealerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="dealerModalLabel">
                                                {{ labels('front_messages.become_dealer', 'Become a dealer') }}</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="dealer-form" method="post" action="{{ route('my-account.user-status.send_dealer_request') }}">
                                            <div class="form-row row-cols-lg-2 row-cols-md-2 row-cols-sm-1 row-cols-1">
                                                <div class="form-group">
                                                    <label for="first_name"
                                                        class="">{{ labels('front_messages.first_name', 'First Name') }}</label>
                                                    <input name="first_name"
                                                        placeholder="First Name" value="{{ $user_info->first_name }}" id="first_name"
                                                        type="text" />
                                                </div>
                                                <div class="form-group" wire:ignore>
                                                    <label for="last_name"
                                                        class="">{{ labels('front_messages.last_name', 'Last Name') }}</label>
                                                    <input name="last_name"
                                                        placeholder="Last Name" value="{{ $user_info->last_name }}" id="last_name"
                                                        type="text" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="birthdate" class="">{{ labels('front_messages.birthdate', 'Birthdate') }}</label>
                                                    <input wire:model="dealer_birthdate" type="text" class="form-control datepicker" name="dealer_birthdate"
                                                        id="dealer_birthdate" placeholder="Birthdate" value="{{ $user_info->birthdate }}" autocomplete="off" />
                                                    @error('dealer_birthdate')
                                                    <span class="error_msg">{{ $message }} </span>
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="message" class="">{{ labels('front_messages.your_comments', 'Your comments') }}</label>
                                                    <textarea wire:model="message" name="message" id="message"></textarea>
                                                </div>


                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="submit"
                                                    class="btn btn-primary m-0 send_dealer_request"><span>{{ labels('front_messages.send_request', 'Send Request') }}</span></button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Dealer Modal -->

                            {{-- Manager Modal --}}
                            <div wire:ignore.self class="modal fade" id="managerModal" tabindex="-1"
                                aria-labelledby="managerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title" id="managerModalLabel">
                                                {{ labels('front_messages.become_manager', 'Become a Manager') }}</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form wire:submit="send_manager_request" action="">
                                            <div class="form-row row-cols-lg-2 row-cols-md-2 row-cols-sm-1 row-cols-1">
                                                <div class="form-group">
                                                    <label for="first_name" class="">{{ labels('front_messages.first_name', 'First Name') }}</label>
                                                    <input wire:model="first_name" name="first_name" placeholder="First Name" value="{{ $user_info->first_name }}"
                                                        id="first_name" type="text" />
                                                        @error('first_name')
                                                        <span class="error_msg">{{ $message }} </span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="last_name" class="">{{ labels('front_messages.last_name', 'Last Name') }}</label>
                                                    <input wire:model="last_name" name="last_name" placeholder="Last Name" value="{{ $user_info->last_name }}"
                                                        id="last_name" type="text" />
                                                        @error('last_name')
                                                        <span class="error_msg">{{ $message }} </span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="birthdate" class="">{{ labels('front_messages.birthdate', 'Birthdate') }}</label>
                                                    <input wire:model="manager_birthdate" type="text" class="form-control datepicker" name="manager_birthdate" id="manager_birthdate"
                                                        placeholder="Birthdate" autocomplete="off" />
                                                        @error('manager_birthdate')
                                                        <span class="error_msg">{{ $message }} </span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="passport" class="">{{ labels('front_messages.passport', 'Passport') }}</label>
                                                    <input wire:model="passport" type="text" class="form-control" name="passport" id="passport"
                                                        placeholder="Enter your passport number" />
                                                        @error('passport')
                                                        <span class="error_msg">{{ $message }} </span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="tax_id" class="">{{ labels('front_messages.tax_id', 'Tax ID') }}</label>
                                                    <input wire:model="tax_id" type="text" class="form-control" name="tax_id" id="tax_id"
                                                        placeholder="Enter your tax ID" />
                                                        @error('tax_id')
                                                        <span class="error_msg">{{ $message }} </span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="photos" class="">{{ labels('front_messages.photo', 'Photos of documents') }}</label>
                                                        <div class="photo-preview-container mb-3">
                                                            @if ($photoUrls && count($photoUrls) > 0)
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    @foreach ($photoUrls as $index => $photo)
                                                                        <div class="position-relative">
                                                                            <img class="img-thumbnail" width="100" height="100"
                                                                                 src="{{ $photo['temporaryUrl'] }}"
                                                                                 alt="{{ $photo['name'] }}">
                                                                            <button type="button"
                                                                                    class="btn-close p-0 btn-danger btn-sm position-absolute top-0 end-0"
                                                                                    wire:click="removePhoto({{ $index }})">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                    @endforeach
                                                    </div>
                                                    @endif
                                                        </div>
                                                        <div wire:loading wire:target="newPhotos">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            {{ labels('front_messages.uploading', 'Uploading...') }}
                                                        </div>
                                                        <input wire:model.live="newPhotos"
                                                               type="file"
                                                               class="form-control"
                                                               multiple
                                                               accept="image/*">
                                                        @error('newPhotos')
                                                            <span class="error_msg">{{ $message }}</span>
                                                        @enderror
                                                        @error('newPhotos.*')
                                                            <span class="error_msg">{{ $message }}</span>
                                                        @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="message" class="">{{ labels('front_messages.your_comments', 'Your comments') }}</label>
                                                    <textarea wire:model="message" name="message" id="message"></textarea>
                                                </div>


                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="submit"
                                                    class="btn btn-primary m-0 send_manager_request"><span>{{ labels('front_messages.send_request', 'Send Request') }}</span></button>
                                            </div>
                                            {{-- </form> --}}
                                            </form>
                                            <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        if (typeof $.fn.datepicker !== 'undefined') {
                                                    $('.datepicker').datepicker({
                                                        autoclose: true,
                                                        format: 'yyyy-mm-dd',
                                                                todayHighlight: true
                                                            }).on('change', function(e) {
                                                                @this.set('manager_birthdate', e.target.value);
                                                            });
                                                        }

                                                        // Додаємо обробник для відкриття модального вікна з фото
                                                        $('.preview-photo').click(function() {
                                                            var photoUrl = $(this).data('photo-url');
                                                            var photoTitle = $(this).data('photo-title');
                                                            $('#modalPhoto').attr('src', photoUrl);
                                                            $('#photoPreviewModalLabel').text(photoTitle);
                                                            var modal = new bootstrap.Modal(document.getElementById('photoPreviewModal'));
                                                            modal.show();
                                                        });

                                                        // Додаємо слухач події для закриття модального вікна
                                                        Livewire.on('closeModal', () => {
                                                            const modal = bootstrap.Modal.getInstance(document.getElementById('managerModal'));
                                                            if (modal) {
                                                                modal.hide();
                                                            }
                                                        });

                                                        // Додаємо слухач події для оновлення сторінки
                                                        Livewire.on('refresh-page', () => {
                                                            window.location.reload();
                                                        });
                                                    });
                                            </script>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Manager Modal -->
                            @endif

                            @if ($user_status)
                            <div class="row">
                                <div class="col-12">
                                        <h2>You have already sent a request.</h2>
                                    <h3>Information about your request</h3>
                                        <p>New role: <span class="badge bg-primary">{{ $user_status->type }}</span></p>
                                        <p>Request status: <span class="badge bg-secondary">{{ $user_status->status }}</span></p>
                                    @if($user_status->type == 'manager')
                                    @if ($user_status->photos)
                                        <div>
                                            <p>Photos of documents:</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ((array)$user_status->photos as $photo)
                                                        <div class="position-relative">
                                                            <img class="img-thumbnail cursor-pointer preview-photo" width="100" height="100"
                                                                 src="{{ is_array($photo) ? $photo['url'] : $photo }}"
                                                                 alt="Document Photo"
                                                                 data-photo-url="{{ is_array($photo) ? $photo['url'] : $photo }}"
                                                                 data-photo-title="Document Photo">
                                                        </div>
                                            @endforeach
                                                </div>
                                        </div>
                                    @endif
                                    @endif
                                    <p>Message: {{ $user_status->message }}</p>
                                    </div>
                                </div>
                                @endif

                                {{-- Архів заявок --}}
                                @if($archived_requests && $archived_requests->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h3>{{ labels('front_messages.request_history', 'Request History') }}</h3>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ labels('front_messages.type', 'Type') }}</th>
                                                        <th>{{ labels('front_messages.status', 'Status') }}</th>
                                                        <th>{{ labels('front_messages.message', 'Message') }}</th>
                                                        <th>{{ labels('front_messages.documents', 'Documents') }}</th>
                                                        <th>{{ labels('front_messages.notes', 'Notes') }}</th>
                                                        <th>{{ labels('front_messages.created_at', 'Created At') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($archived_requests as $request)
                                                        <tr>
                                                            <td>{{ ucfirst($request->type) }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : 'danger' }}">
                                                                    {{ ucfirst($request->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ Str::limit($request->message, 50) }}</td>
                                                            <td>
                                                                @if(!empty($request->photos))
                                                                    <div class="d-flex gap-2">
                                                                        @foreach((array)$request->photos as $photo)
                                                                            <div class="position-relative">
                                                                                <img class="img-thumbnail"
                                                                                     width="50" height="50"
                                                                                     src="{{ is_array($photo) ? $photo['url'] : $photo }}"
                                                                                     alt="Document Photo"
                                                                                     style="object-fit: cover;">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">{{ labels('front_messages.no_documents', 'No documents') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $request->notes ?? '-' }}</td>
                                                            <td>{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal for Photo Preview -->
    <div wire:ignore class="modal fade" id="photoPreviewModal" tabindex="-1" aria-labelledby="photoPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoPreviewModalLabel">Photo Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css">

    <style>
    .cursor-pointer {
        cursor: pointer;
    }
    .cursor-pointer:hover {
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    /* Стилі для модальних вікон */
    .modal-backdrop {
        opacity: 0.5 !important;
    }

    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Видаляємо функції для відкриття модального вікна з фото

        // Додаємо обробник для видалення modal-backdrop
        const managerModal = document.getElementById('managerModal');
        if (managerModal) {
            managerModal.addEventListener('hidden.bs.modal', function () {
                const modalBackdrops = document.getElementsByClassName('modal-backdrop');
                while(modalBackdrops.length > 0) {
                    modalBackdrops[0].remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }

        // Додаємо такий самий обробник для модального вікна дилера
        const dealerModal = document.getElementById('dealerModal');
        if (dealerModal) {
            dealerModal.addEventListener('hidden.bs.modal', function () {
                const modalBackdrops = document.getElementsByClassName('modal-backdrop');
                while(modalBackdrops.length > 0) {
                    modalBackdrops[0].remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }

        // Закриваємо модальне вікно менеджера
        Livewire.on('closeModal', function() {
            const managerModal = document.getElementById('managerModal');
            if (managerModal) {
                const bsModal = bootstrap.Modal.getInstance(managerModal);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        });

        // Оновлюємо сторінку
        Livewire.on('refresh-page', function() {
            setTimeout(function() {
                window.location.reload();
            }, 500);
        });

        // Ініціалізація datepicker
        if (typeof $.fn.datepicker !== 'undefined') {
            $('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            }).on('change', function(e) {
                @this.set('manager_birthdate', e.target.value);
            });
        }

        // Функція для відкриття модального вікна з фото
        window.openPhotoModal = function(url, title) {
            const modalElement = document.getElementById('photoPreviewModal');
            if (!modalElement) {
                console.error('Modal element not found');
                return;
            }

            const modalPhoto = document.getElementById('modalPhoto');
            const modalTitle = document.getElementById('photoPreviewModalLabel');

            if (modalPhoto && modalTitle) {
                modalPhoto.src = url;
                modalTitle.textContent = title || 'Photo Preview';
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        };

        // Додаємо обробники для всіх фото
        function attachPhotoHandlers() {
            const photos = document.querySelectorAll('.preview-photo');
            photos.forEach(function(photo) {
                photo.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-photo-url');
                    const title = this.getAttribute('data-photo-title');
                    window.openPhotoModal(url, title);
                });
            });
        }

        // Початкове підключення обробників
        attachPhotoHandlers();

        // Оновлюємо обробники після оновлення фото через Livewire
        Livewire.on('photos-updated', function() {
            setTimeout(attachPhotoHandlers, 100);
        });
    });
    </script>
</div>
