@extends('admin/layout')

@section('title')
    {{ labels('admin_labels.manage_return_requests', 'Return Requests') }}
@endsection

@section('content')
    <section class="main-content">
        <div class="row">
            <div class="d-flex row align-items-center">
                <div class="col-md-6 page-info-title">
                    <h3>{{ labels('admin_labels.manage_return_requests', 'Return Requests') }}</h3>
                    <p class="sub_title">
                        {{ labels('admin_labels.all_information_about_return_requests', 'All Information About Return Requests') }}
                    </p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <nav aria-label="breadcrumb" class="float-end">
                        <ol class="breadcrumb">
                            <i class='bx bx-home-smile'></i>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a></li>
                            <li class="breadcrumb-item">
                                {{ labels('admin_labels.manage_return_requests', 'Return Requests') }}</li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ labels('admin_labels.return_requests', 'Return Requests') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="overview-data">
            <div class="card content-area p-4 mb-5">
                <div class="row mb-5">
                    <div class="col-md-12">
                        <div class="heading">
                            <h4>{{ labels('admin_labels.return_requests_review', 'Return Requests Review') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-sm-12 col mb-6">
                        <div class="info-box align-items-center">
                            <div class="primary-icon">
                                <i class="fa fa-hourglass-half fa-2x text-warning"></i>
                            </div>
                            <div class="content">
                                <p class="body-default">{{ labels('admin_labels.return_request_pending', 'Pending') }}</p>
                                <h5>{{ returnRequestsCount(0) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-sm-12 col mb-6">
                        <div class="info-box align-items-center">
                            <div class="success-icon">
                                <i class="fa fa-check-circle fa-2x text-success"></i>
                            </div>
                            <div class="content">
                                <p class="body-default">{{ labels('admin_labels.return_request_approved', 'Approved') }}
                                </p>
                                <h5>{{ returnRequestsCount(1) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-lg-4 col-md-6 col-sm-12 col mb-6">
                        <div class="info-box align-items-center">
                            <div class="danger-icon">
                                <i class="fa fa-times-circle fa-2x text-danger"></i>
                            </div>
                            <div class="content">
                                <p class="body-default">{{ labels('admin_labels.return_request_decline', 'Declined') }}</p>
                                <h5>{{ returnRequestsCount(2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="overview-data">
            <div class="card content-area p-4">
                <div class="row align-items-center d-flex heading mb-5">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                <h4>{{ labels('admin_labels.return_requests', 'Return Requests') }}</h4>
                            </div>
                            <div class="col-md-12 col-lg-6 d-flex justify-content-end">
                                <div class="input-group me-2 search-input-grp">
                                    <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                    <input type="text" data-table="admin_return_request_table"
                                        class="form-control searchInput" placeholder="Search...">
                                    <span class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                                </div>
                                <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                    data-bs-target="#columnFilterOffcanvas" data-table="admin_return_request_table"
                                    dateFilter='true' statusFilter='true' reasonFilter='true'><i
                                        class='bx bx-filter-alt'></i></a>
                                <a class="btn me-2" id="tableRefresh" data-table="admin_return_request_table"><i
                                        class='bx bx-refresh'></i></a>
                                <div class="dropdown">
                                    <a class="btn dropdown-toggle export-btn" type="button" id="exportOptionsDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-download'></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                        <li><button class="dropdown-item" type="button"
                                                onclick="exportTableData('admin_return_request_table','csv')">CSV</button>
                                        </li>
                                        <li><button class="dropdown-item" type="button"
                                                onclick="exportTableData('admin_return_request_table','json')">JSON</button>
                                        </li>
                                        <li><button class="dropdown-item" type="button"
                                                onclick="exportTableData('admin_return_request_table','sql')">SQL</button>
                                        </li>
                                        <li><button class="dropdown-item" type="button"
                                                onclick="exportTableData('admin_return_request_table','excel')">Excel</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="pt-0">
                                    <div id="return_requests_table">
                                        <div class="table-responsive">
                                            <table id="admin_return_request_table" data-toggle="table"
                                                data-loading-template="loadingTemplate"
                                                data-url="{{ route('admin.return_requests.list') }}"
                                                data-click-to-select="true" data-side-pagination="server"
                                                data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                                data-search="false" data-show-columns="false" data-show-refresh="false"
                                                data-trim-on-search="false" data-sort-name="rr.id" data-sort-order="desc"
                                                data-mobile-responsive="true" data-toolbar="" data-show-export="false"
                                                data-maintain-selected="true" data-query-params="queryParams">
                                                <thead>
                                                    <tr>
                                                        <th data-field="id" data-sortable="true"
                                                            data-footer-formatter="totalFormatter">
                                                            {{ labels('admin_labels.id', 'ID') }}
                                                        </th>
                                                        <th data-field="return_request_id" data-sortable="true">
                                                            {{ labels('admin_labels.return_request_id', 'Return Request ID') }}
                                                        </th>
                                                        <th data-field="order_id" data-sortable="true">
                                                            {{ labels('admin_labels.order_id', 'Order ID') }}
                                                        </th>
                                                        <th data-field="order_item_id" data-sortable="true">
                                                            {{ labels('admin_labels.order_item_id', 'Order Item ID') }}
                                                        </th>
                                                        <th data-field="user_id" data-sortable="true"
                                                            data-visible="false">
                                                            {{ labels('admin_labels.user_id', 'User ID') }}
                                                        </th>
                                                        <th data-field="username" data-sortable="false">
                                                            {{ labels('admin_labels.user_name', 'User Name') }}
                                                        </th>
                                                        <th data-field="product_name" data-sortable="false">
                                                            {{ labels('admin_labels.product_name', 'Product Name') }}
                                                        </th>
                                                        <th data-field="reason" data-sortable="false">
                                                            {{ labels('admin_labels.reason', 'Reason') }}
                                                        </th>
                                                        <th data-field="refund_amount" data-sortable="true">
                                                            {{ labels('admin_labels.refund_amount', 'Refund Amount') }}
                                                        </th>
                                                        <th data-field="status" data-sortable="false">
                                                            {{ labels('admin_labels.status', 'Status') }}
                                                        </th>
                                                        <th data-field="date_added" data-sortable="true">
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
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal for Return Request Details -->
        <div class="modal fade" id="view_return_request_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ labels('admin_labels.return_request_details', 'Return Request Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> <span id="modal_return_id"></span></p>
                                <p><strong>User ID:</strong> <span id="modal_user_id"></span></p>
                                <p><strong>Username:</strong> <span id="modal_username"></span></p>
                                <p><strong>Order ID:</strong> <span id="modal_order_id"></span></p>
                                <p><strong>Order Item ID:</strong> <span id="modal_order_item_id"></span></p>
                                <p><strong>Product ID:</strong> <span id="modal_product_id"></span></p>
                                <p><strong>Product Variant ID:</strong> <span id="modal_product_variant_id"></span></p>
                                <p><strong>Product Name:</strong> <span id="modal_product_name"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Reason:</strong> <span id="modal_reason"></span></p>
                                <p><strong>Status:</strong> <span id="modal_status"></span></p>
                                <p><strong>Remarks:</strong> <span id="modal_remarks"></span></p>
                                <p><strong>Delivery Status:</strong> <span id="modal_delivery_status"></span></p>
                                <p><strong>Application Type:</strong> <span id="modal_application_type"></span></p>
                                <p><strong>Refund Amount:</strong> <span id="modal_refund_amount"></span></p>
                                <p><strong>Refund Method:</strong> <span id="modal_refund_method"></span></p>
                                <p><strong>Return Method:</strong> <span id="modal_return_method"></span></p>
                            </div>
                            <div class="col-md-12">
                                <p><strong>Description:</strong> <span id="modal_description"></span></p>
                                <p><strong>Evidence:</strong> <span id="modal_evidence"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="action_buttons" style="display: none;">
                            <button type="button" class="btn btn-success me-2 approve-return" data-id=""><i
                                    class="fa fa-check"></i> {{ labels('admin_labels.approve', 'Approve') }}</button>
                            <button type="button" class="btn btn-danger me-2 decline-return" data-id=""><i
                                    class="fa fa-times"></i> {{ labels('admin_labels.decline', 'Decline') }}</button>
                        </div>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ labels('admin_labels.close', 'Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.view-return', function() {
            $('#modal_return_id').text($(this).data('id'));
            $('#modal_user_id').text($(this).data('user-id'));
            $('#modal_username').text($(this).data('username'));
            $('#modal_order_id').text($(this).data('order-id'));
            $('#modal_order_item_id').text($(this).data('order-item-id'));
            $('#modal_product_id').text($(this).data('product-id'));
            $('#modal_product_variant_id').text($(this).data('product-variant-id'));
            $('#modal_product_name').text($(this).data('product-name'));
            $('#modal_reason').text($(this).data('reason'));
            $('#modal_status').text($(this).data('status') == 0 ? 'Pending' : ($(this).data('status') == 1 ?
                'Approved' : 'Declined'));
            $('#modal_remarks').text($(this).data('remarks') || 'N/A');
            $('#modal_delivery_status').text($(this).data('delivery-status') || 'N/A');
            $('#modal_application_type').text($(this).data('application-type') || 'N/A');
            $('#modal_refund_amount').text($(this).data('refund-amount') || 'N/A');
            $('#modal_refund_method').text($(this).data('refund-method') || 'N/A');
            $('#modal_description').text($(this).data('description') || 'N/A');
            $('#modal_return_method').text($(this).data('return-method') || 'N/A');

            // Обробка evidence_path
            var evidencePath = $(this).data('evidence-path');
            if (evidencePath) {
                try {
                    var paths = JSON.parse(evidencePath);
                    var evidenceHtml = '';
                    paths.forEach(function(path) {
                        evidenceHtml += '<a href="' + path + '" data-lightbox="evidence-' + $(this).data(
                                'id') + '"><img src="' + path +
                            '" style="max-width: 100px; margin-right: 10px;"></a>';
                    }.bind(this));
                    $('#modal_evidence').html(evidenceHtml);
                } catch (e) {
                    $('#modal_evidence').text('Error parsing evidence');
                }
            } else {
                $('#modal_evidence').text('No evidence');
            }

            // Показ кнопок для Pending
            if ($(this).data('status') == 0) {
                $('#action_buttons').show();
                $('.approve-return').data('id', $(this).data('id'));
                $('.decline-return').data('id', $(this).data('id'));
            } else {
                $('#action_buttons').hide();
            }
        });

        $(document).on('click', '.approve-return', function() {
            var id = $(this).data('id');
            $.post('{{ route('admin.return_requests.approve', ':id') }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                alert(response.message);
                $('#view_return_request_modal').modal('hide');
                $('#admin_return_request_table').bootstrapTable('refresh');
            });
        });

        $(document).on('click', '.decline-return', function() {
            var id = $(this).data('id');
            $.post('{{ route('admin.return_requests.decline', ':id') }}'.replace(':id', id), {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                alert(response.message);
                $('#view_return_request_modal').modal('hide');
                $('#admin_return_request_table').bootstrapTable('refresh');
            });
        });

        function queryParams(params) {
            // Переконуємося, що pageNumber і pageSize є валідними числами
            const pageNumber = parseInt(params.pageNumber) || 1;
            const pageSize = parseInt(params.pageSize) || 10;
            const paginationOffset = (pageNumber - 1) * pageSize;

            return {
                search: params.search || '',
                limit: pageSize,
                pagination_offset: isNaN(paginationOffset) ? 0 : paginationOffset,
                sort: params.sortName || 'rr.id',
                order: params.sortOrder || 'desc',
                start_date: $('#start_date').val() || '',
                end_date: $('#end_date').val() || '',
                status: $('#status').val() || '',
                reason: $('#reason').val() || ''
            };
        }

        $('#applyFilter').click(function() {
            $('#admin_return_request_table').bootstrapTable('refresh');
        });

        $('#resetFilter').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status').val('');
            $('#reason').val('');
            $('#admin_return_request_table').bootstrapTable('refresh');
        });
    </script>
@endsection
