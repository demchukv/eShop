@extends('admin/layout')

@section('title')
    {{ labels('admin_labels.balance', 'Balance') }}
@endsection

@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.balance', 'Balance') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.manage_commission_distributions', 'Manage Commission Distributions') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.balance', 'Balance') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="overview-data">
        <div class="card content-area p-4">
            <div class="row align-items-center d-flex heading mb-5">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <h4>{{ labels('admin_labels.commission_distributions', 'Commission Distributions') }}</h4>
                        </div>
                        <div class="col-md-12 col-lg-6 d-flex justify-content-end align-items-center flex-wrap">
                            {{-- <div class="input-group me-2 search-input-grp" style="max-width: 200px;">
                                <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                <input type="text" id="search" class="form-control" placeholder="Search...">
                            </div> --}}
                            <select id="status_filter" class="form-control me-2" style="max-width: 150px;">
                                <option value="">All Statuses</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="number" id="user_id_filter" class="form-control me-2" placeholder="User ID"
                                style="max-width: 100px;">
                            <select id="month_filter" class="form-control me-2" style="max-width: 120px;">
                                @foreach ($months as $i => $name)
                                    <option value="{{ $i }}" {{ $i == $defaultMonth ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                            <select id="year_filter" class="form-control me-2" style="max-width: 100px;">
                                @for ($i = $minYear; $i <= $maxYear; $i++)
                                    <option value="{{ $i }}" {{ $i == $defaultYear ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                            <a class="btn me-2" id="tableRefresh" data-table="admin_balance_table"><i
                                    class='bx bx-refresh'></i></a>
                            <div class="dropdown">
                                <a class="btn dropdown-toggle export-btn" type="button" id="exportOptionsDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-download'></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                    <li><button class="dropdown-item" type="button"
                                            onclick="exportTableData('admin_balance_table','csv')">CSV</button></li>
                                    <li><button class="dropdown-item" type="button"
                                            onclick="exportTableData('admin_balance_table','json')">JSON</button></li>
                                    <li><button class="dropdown-item" type="button"
                                            onclick="exportTableData('admin_balance_table','sql')">SQL</button></li>
                                    <li><button class="dropdown-item" type="button"
                                            onclick="exportTableData('admin_balance_table','excel')">Excel</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Блок підсумків -->
            <div class="row mb-4" id="summary-block">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Summary for <span id="for_my"></span></h5>
                            <p>Pending: <span id="summary-pending">0</span></p>
                            <p>Completed: <span id="summary-completed">0</span></p>
                            <p>Canceled: <span id="summary-canceled">0</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8" id="user-id-one-summary" style="display: none;">
                    <div class="row">
                        @foreach ($userIdSubOptions as $sub => $label)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">User ID 1: {{ $label }}</h5>

                                        <p>Pending: <span id="user1-{{ $sub }}-pending">0</span></p>
                                        <p>Completed: <span id="user1-{{ $sub }}-completed">0</span></p>
                                        <p>Canceled: <span id="user1-{{ $sub }}-canceled">0</span></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="pt-0">
                        <div class="table-responsive">
                            <table class="table" id="admin_balance_table" data-toggle="table"
                                data-loading-template="loadingTemplate" data-url="{{ route('admin.balance.list') }}"
                                data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                                data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-show-columns="false"
                                data-show-refresh="false" data-trim-on-search="false" data-sort-name="id"
                                data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="false" data-maintain-selected="true"
                                data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="id" data-sortable="true">{{ labels('admin_labels.id', 'ID') }}
                                        </th>
                                        <th data-field="order_id" data-sortable="true">
                                            {{ labels('admin_labels.order_id', 'Order ID') }}</th>
                                        <th data-field="user_id" data-sortable="true">
                                            {{ labels('admin_labels.user_id', 'User ID') }}</th>
                                        <th data-field="user_id_sub" data-sortable="true">
                                            {{ labels('admin_labels.user_id_sub', 'User ID Sub') }}</th>
                                        <th data-field="amount" data-sortable="true">
                                            {{ labels('admin_labels.amount', 'Amount') }}</th>
                                        <th data-field="message" data-sortable="false">
                                            {{ labels('admin_labels.message', 'Message') }}</th>
                                        <th data-field="status" data-sortable="true">
                                            {{ labels('admin_labels.status', 'Status') }}</th>
                                        <th data-field="created_at" data-sortable="true">
                                            {{ labels('admin_labels.created_at', 'Created At') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        // Define queryParams function in global scope
        window.queryParams = function(params) {
            params.status_filter = $('#status_filter').val() || '';
            params.user_id_filter = $('#user_id_filter').val() || '';
            params.month_filter = $('#month_filter').val() || '{{ $defaultMonth }}';
            params.year_filter = $('#year_filter').val() || '{{ $defaultYear }}';

            console.log('Query Params:', params); // Debug log
            return params;
        };

        $(document).ready(function() {
            // Ensure table uses queryParams
            $('#admin_balance_table').bootstrapTable({
                url: '{{ route('admin.balance.list') }}',
                queryParams: window.queryParams // Explicitly assign the function
            });

            // Refresh table when filters change
            $('#status_filter, #user_id_filter, #month_filter, #year_filter').on('change', function() {
                console.log('Filter changed:', {
                    status_filter: $('#status_filter').val(),
                    user_id_filter: $('#user_id_filter').val(),
                    month_filter: $('#month_filter').val(),
                    year_filter: $('#year_filter').val()
                });
                $('#admin_balance_table').bootstrapTable('refresh', {
                    query: window.queryParams({}) // Pass query params explicitly
                });
            });

            // Handle table refresh button
            $('#tableRefresh').on('click', function() {
                $('#admin_balance_table').bootstrapTable('refresh', {
                    query: window.queryParams({}) // Pass query params explicitly
                });
            });

            // Log the response data
            $('#admin_balance_table').on('load-success.bs.table', function(e, data) {
                console.log('Table loaded with data:', data);
                $('#for_my').text(data.month + ' ' + data.year);
                // Display formatted summary amounts directly
                $('#summary-pending').text(data.summary[
                    '{{ \App\Models\CommissionDistribution::STATUS_PENDING }}'] || '0.00');
                $('#summary-completed').text(data.summary[
                    '{{ \App\Models\CommissionDistribution::STATUS_COMPLETED }}'] || '0.00');
                $('#summary-canceled').text(data.summary[
                    '{{ \App\Models\CommissionDistribution::STATUS_CANCELED }}'] || '0.00');

                // Show #user-id-one-summary only when user_id_filter is explicitly "1"
                if (data.user_id_one_summary && $('#user_id_filter').val() === '1') {
                    $('#user-id-one-summary').show();
                    @foreach ($userIdSubOptions as $sub => $label)
                        $('#user1-{{ $sub }}-pending').text(data.user_id_one_summary[
                            '{{ $sub }}']?.[
                            '{{ \App\Models\CommissionDistribution::STATUS_PENDING }}'
                        ] || '0.00');
                        $('#user1-{{ $sub }}-completed').text(data.user_id_one_summary[
                            '{{ $sub }}']?.[
                            '{{ \App\Models\CommissionDistribution::STATUS_COMPLETED }}'
                        ] || '0.00');
                        $('#user1-{{ $sub }}-canceled').text(data.user_id_one_summary[
                            '{{ $sub }}']?.[
                            '{{ \App\Models\CommissionDistribution::STATUS_CANCELED }}'
                        ] || '0.00');
                    @endforeach
                } else {
                    $('#user-id-one-summary').hide();
                }
            });

            // Log errors if the table fails to load
            $('#admin_balance_table').on('load-error.bs.table', function(e, status, res) {
                console.error('Table load error:', status, res);
            });
        });
    </script>
@endsection
