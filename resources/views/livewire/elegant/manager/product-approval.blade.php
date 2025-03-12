<div>
    @php
        $bread_crumb['page_main_bread_crumb'] = 'Pending Product Approvals';
    @endphp

    <div id="page-content">
        <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />

        <div class="container-fluid">
            <div class="row">
                <x-utility.my_account_slider.account_slider :$user_info />
                <!-- Products Table -->
                <div class="card col-12 col-sm-12 col-md-12 col-lg-9">
                    <div class="card-body">
                        <h1>Pending Product Approvals</h1>

                        <!-- Search Bar -->
                        <div class="row mb-3">

                            <div class="col-md-4">
                                <input type="text" wire:model.live="search" class="form-control"
                                    placeholder="Search by name or product identity...">
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Seller</th>
                                    <th>Approvals</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td><a target="_blank"
                                                href="{{ route('products.details', $product->slug) }}">{{ $product->name }}</a>
                                        </td>
                                        <td>{{ $product->user ? $product->user->username : 'N/A' }}</td>
                                        <td>{{ $product->approvals_count }}/10</td>
                                        <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <button wire:click="approveProduct({{ $product->id }})"
                                                class="btn btn-sm btn-primary" wire:loading.attr="disabled">
                                                Approve
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No products pending approval.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.addEventListener('show-success', function(event) {
                iziToast.success({
                    message: event.detail.message,
                    position: 'topRight'
                });
            });

            window.addEventListener('show-error', function(event) {
                iziToast.error({
                    message: event.detail.message,
                    position: 'topRight'
                });
            });
        </script>
    @endpush
</div>
