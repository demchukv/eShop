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
    </section>
@endsection

@section('scripts')
    <script>
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
