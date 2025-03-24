@extends('seller/layout')
@section('title')
    {{ labels('admin_labels.pending_payments', 'Pending Payments') }}
@endsection
@section('content')
    <section class="main-content">
        <div class="row">
            <div class="d-flex row align-items-center">
                <div class="col-md-6 page-info-title">
                    <h3>{{ labels('admin_labels.pending_payments', 'Pending Payments') }}</h3>
                    <p class="sub_title">
                        {{ labels('admin_labels.track_pending_payments', 'Track Pending Payments for Your Sales') }}
                    </p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <nav aria-label="breadcrumb" class="float-end">
                        <ol class="breadcrumb">
                            <i class='bx bx-home-smile'></i>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('seller.home') }}">{{ labels('admin_labels.home', 'Home') }}</a></li>
                            <li class="breadcrumb-item">{{ labels('admin_labels.wallet_management', 'Wallet Management') }}
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ labels('admin_labels.pending_payments', 'Pending Payments') }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <section class="overview-data">
                <div class="card content-area p-4 ">
                    <div class="row align-items-center d-flex heading mb-5">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <h4>{{ labels('admin_labels.manage_pending_payments', 'Manage Pending Payments') }}</h4>
                                </div>
                                <div class="col-md-12 col-lg-6 d-flex justify-content-end ">
                                    <div class="input-group me-3 search-input-grp ">
                                        <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                        <input type="text" data-table="seller_pending_payments_table"
                                            class="form-control searchInput" placeholder="Search...">
                                        <span class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                                    </div>
                                    <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                        data-bs-target="#columnFilterOffcanvas" data-table="seller_pending_payments_table"
                                        dateFilter='true' orderStatusFilter='false' paymentMethodFilter='false'
                                        orderTypeFilter='false'><i class='bx bx-filter-alt'></i></a>
                                    <a class="btn me-2" id="tableRefresh" data-table="seller_pending_payments_table"><i
                                            class='bx bx-refresh'></i></a>
                                    <div class="dropdown">
                                        <a class="btn dropdown-toggle export-btn" type="button" id="exportOptionsDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bx-download'></i>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                            <li><button class="dropdown-item" type="button"
                                                    onclick="exportTableData('seller_pending_payments_table','csv')">CSV</button>
                                            </li>
                                            <li><button class="dropdown-item" type="button"
                                                    onclick="exportTableData('seller_pending_payments_table','json')">JSON</button>
                                            </li>
                                            <li><button class="dropdown-item" type="button"
                                                    onclick="exportTableData('seller_pending_payments_table','sql')">SQL</button>
                                            </li>
                                            <li><button class="dropdown-item" type="button"
                                                    onclick="exportTableData('seller_pending_payments_table','excel')">Excel</button>
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
                                    <table class='table-striped' id='seller_pending_payments_table' data-toggle="table"
                                        data-loading-template="loadingTemplate"
                                        data-url="{{ route('seller.pending_payments.list') }}" data-click-to-select="true"
                                        data-side-pagination="server" data-pagination="true"
                                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                        data-show-columns="false" data-show-refresh="false" data-trim-on-search="false"
                                        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                                        data-toolbar="" data-show-export="false" data-maintain-selected="true"
                                        data-export-types='["txt","excel","csv"]'
                                        data-export-options='{
                                            "fileName": "pending-payments-list",
                                            "ignoreColumn": ["state"]
                                        }'
                                        data-query-params="seller_pending_payments_query_params">
                                        <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true">
                                                    {{ labels('admin_labels.id', 'ID') }}
                                                </th>
                                                <th data-field="order_id" data-sortable="false">
                                                    {{ labels('admin_labels.order_id', 'Order ID') }}
                                                </th>
                                                <th data-field="amount" data-sortable="false">
                                                    {{ labels('admin_labels.amount', 'Amount') }}
                                                </th>
                                                <th data-field="message" data-sortable="false">
                                                    {{ labels('admin_labels.message', 'Message') }}
                                                </th>
                                                <th data-field="status" data-sortable="false">
                                                    {{ labels('admin_labels.status', 'Status') }}
                                                </th>
                                                <th data-field="created_at" data-sortable="false">
                                                    {{ labels('admin_labels.date', 'Date') }}
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
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        function seller_pending_payments_query_params(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
    </script>
@endsection
