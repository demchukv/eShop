@extends('admin/layout')
@section('title')
{{ labels('admin_labels.user_status', 'User Status') }}
@endsection
@section('content')

<div class="d-flex row align-items-center">
    <div class="col-md-6 col-xl-6 page-info-title">
        <h3>{{ labels('admin_labels.user_status', 'User Status') }}
        </h3>
        <p class="sub_title">
            {{ labels('admin_labels.manage_requests_to_change_user_status', 'Manage Requests to Change User Status') }}
        </p>
    </div>
    <div class="col-md-6 col-xl-6 d-flex justify-content-end">
        <nav aria-label="breadcrumb" class="float-end">
            <ol class="breadcrumb">
                <i class='bx bx-home-smile'></i>
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home')
                        }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ labels('admin_labels.user_status', 'User Status') }}
                </li>
            </ol>
        </nav>
    </div>
</div>

{{-- table --}}

<section
    class="overview-data {{ $user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view user_status') ? '' : 'd-none' }}">
    <div class="card content-area p-4 ">
        <div class="row align-items-center d-flex heading mb-5">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <h4>{{ labels('admin_labels.user_status', 'User Status') }}
                        </h4>
                    </div>
                    <div class="col-md-12 col-lg-6 d-flex justify-content-end ">
                        <div class="input-group me-2 search-input-grp ">
                            <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                            <input type="text" data-table="admin_user_status_table" class="form-control searchInput"
                                placeholder="Search...">
                            <span class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                        </div>
                        <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                            data-bs-target="#columnFilterOffcanvas" data-table="admin_user_status_table"
                            dateFilter='false' orderStatusFilter='false' paymentMethodFilter='false'
                            orderTypeFilter='false'><i class='bx bx-filter-alt'></i></a>
                        <a class="btn me-2" id="tableRefresh" data-table="admin_user_status_table"><i
                                class='bx bx-refresh'></i></a>
                        <div class="dropdown">
                            <a class="btn dropdown-toggle export-btn" type="button" id="exportOptionsDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class='bx bx-download'></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                <li><button class="dropdown-item" type="button"
                                        onclick="exportTableData('admin_user_status_table','csv')">CSV</button></li>
                                <li><button class="dropdown-item" type="button"
                                        onclick="exportTableData('admin_user_status_table','json')">JSON</button>
                                </li>
                                <li><button class="dropdown-item" type="button"
                                        onclick="exportTableData('admin_user_status_table','sql')">SQL</button></li>
                                <li><button class="dropdown-item" type="button"
                                        onclick="exportTableData('admin_user_status_table','excel')">Excel</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="pt-0">
                    <div class="table-responsive">
                        <table class='table' id="admin_user_status_table" data-toggle="table"
                            data-loading-template="loadingTemplate" data-url="{{ route('admin.user_status.list') }}"
                            data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-show-columns="false"
                            data-show-refresh="false" data-trim-on-search="false" data-sort-name="id"
                            data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                            data-show-export="false" data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-query-params="queryParams">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">
                                        {{ labels('admin_labels.id', 'ID') }}
                                    <th data-field="user_id" data-disabled="1" data-sortable="true">
                                        {{ labels('admin_labels.user_id', 'User ID') }}
                                    </th>
                                    <th data-field="user_name" data-disabled="1" data-sortable="true">
                                        {{ labels('admin_labels.user_name', 'User Name') }}
                                    </th>
                                    <th data-field="birthdate" data-disabled="1" data-sortable="true">
                                        {{ labels('admin_labels.birthdate', 'Birthdate') }}
                                    </th>
                                    <th data-field="new_role" data-sortable="true">
                                        {{ labels('admin_labels.new_role', 'New Role') }}
                                    </th>
                                    <th data-field="status" data-disabled="1" data-sortable="1">
                                        {{ labels('admin_labels.status', 'Status') }}
                                    </th>
                                    <th data-field="created_at" data-disabled="1" data-sortable="false"data-visible="true">
                                        {{ labels('admin_labels.created_at', 'Created At') }}
                                    </th>
                                    <th data-field="operate" data-sortable="false">
                                        {{ labels('admin_labels.action', 'Action') }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- edit modal -->

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    {{ labels('admin_labels.review_user_status', 'Review User Status Request') }}
                </h5>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <form enctype="multipart/form-data" method="POST" class="submit_form" action="{{ route('admin.user_status.update') }}">
                @method('PUT')
                @csrf
                <input type="hidden" id="edit_status_id" name="id">
                <input type="hidden" id="status" name="status" value="pending">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.first_name', 'First Name') }}</label>
                                <div id="first_name" class="form-text border rounded p-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.email', 'Email') }}</label>
                                <div id="email" class="form-text border rounded p-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.telegram', 'Telegram') }}</label>
                                <div id="telegram" class="form-text border rounded p-2"></div>
                            </div>
<div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.passport', 'Passport') }}</label>
                                <div id="passport" class="form-text border rounded p-2"></div>
                            </div>                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.last_name', 'Last Name') }}</label>
                                <div id="last_name" class="form-text border rounded p-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.mobile', 'Mobile') }}</label>
                                <div id="mobile" class="form-text border rounded p-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.requested_role', 'Requested Role') }}</label>
                                <div id="type" class="form-text border rounded p-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ labels('admin_labels.tax_id', 'Tax ID') }}</label>
                                <div id="tax_id" class="form-text border rounded p-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">{{ labels('admin_labels.message', 'Message') }}</label>
                        <div id="message" class="form-text border rounded p-2" style="min-height: 60px;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">{{ labels('admin_labels.photos', 'Documents') }}</label>
                        <div class="row" id="photos_preview"></div>
                    </div>

                    <div id="notes_container" class="form-group">
                        <label for="notes">{{ labels('admin_labels.notes', 'Notes') }}</label>
                        <div id="notes_text" class="form-text border rounded p-2 d-none" style="min-height: 60px;"></div>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer" id="modal_footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ labels('admin_labels.close', 'Close') }}
                    </button>
                    <button type="button" class="btn btn-danger action-btn" onclick="updateStatus('rejected')">
                        {{ labels('admin_labels.reject', 'Reject') }}
                    </button>
                    <button type="button" class="btn btn-success action-btn" onclick="updateStatus('approved')">
                        {{ labels('admin_labels.approve', 'Approve') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal для перегляду фото -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">{{ labels('admin_labels.document_preview', 'Document Preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="photoModalImage" src="" class="img-fluid" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let photoModal;

        $(document).on('click', '.edit-user-status', function() {
            const id = $(this).data('id');
            const status = $(this).data('status');
            const type = $(this).data('type');
            const firstName = $(this).data('first-name');
            const lastName = $(this).data('last-name');
            const email = $(this).data('email');
            const mobile = $(this).data('mobile');
            const code = $(this).data('country-code');
            const telegram = $(this).data('telegram');
            const passport = $(this).data('passport');
            const taxId = $(this).data('tax-id');
            const message = $(this).data('message');
            const photos = $(this).data('photos');
            const notes = $(this).data('notes');

            $('#edit_status_id').val(id);
            $('#status').val(status);
            $('#type').text(type);
            $('#first_name').text(firstName);
            $('#last_name').text(lastName);
            $('#email').html(email ? `<a href="mailto:${email}" target="_blank">${email}</a>` : '-');
            $('#mobile').html(mobile ? `<a href="tel:+${code}${mobile}" target="_blank">+${code}${mobile}</a>` : '-');
            $('#telegram').html(telegram ? `<a href="https://t.me/${telegram}" target="_blank">@${telegram}</a>` : '-');
            $('#passport').text(passport || '-');
            $('#tax_id').text(taxId || '-');
            $('#message').text(message || '-');

            // Показуємо/приховуємо елементи в залежності від статусу
            if (status === 'pending') {
                $('.action-btn').show();
                $('#notes').show();
                $('#notes_text').addClass('d-none');
            } else {
                $('.action-btn').hide();
                $('#notes').hide();
                $('#notes_text').removeClass('d-none').text(notes || '-');
            }

            // Очищаємо попередні фото
            $('#photos_preview').empty();

            // Додаємо превью фото
            if (photos && photos.length) {
                photos.forEach(photo => {
                    $('#photos_preview').append(`
                        <div class="col-md-4 mb-2">
                            <img src="${photo}" class="img-fluid rounded preview-image"
                                style="max-height: 100px; cursor: pointer"
                                data-photo-url="${photo}">
                        </div>
                    `);
                });
            } else {
                $('#photos_preview').append('<div class="col-12"><p class="text-muted">No documents attached</p></div>');
            }

            const modal = new bootstrap.Modal(document.getElementById('edit_modal'));
            modal.show();
        });

        // Ініціалізуємо модальне вікно для фото
        photoModal = new bootstrap.Modal(document.getElementById('photoModal'));

        // Обробник кліку на превью фото
        $(document).on('click', '.preview-image', function() {
            const photoUrl = $(this).data('photo-url');
            $('#photoModalImage').attr('src', photoUrl);
            photoModal.show();
        });
    });

    function updateStatus(status) {
        $('#status').val(status);
        $('.submit_form').submit();
    }
</script>

@endsection
