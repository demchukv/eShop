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
                                            <button wire:click="openCommentModal({{ $product->id }})"
                                                class="btn btn-sm btn-secondary" wire:loading.attr="disabled">
                                                Add Comment
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

        <!-- Modal -->
        <!-- Modal -->
        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Збільшено розмір модального вікна -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Попередні коментарі -->
                        @if (!empty($comments))
                            <div class="mb-3">
                                <h6>Previous Comments</h6>
                                <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($comments as $comment)
                                        <div class="mb-2">
                                            <strong>{{ $comment['manager']['username'] ?? 'Unknown Manager' }}</strong>
                                            <small
                                                class="text-muted">({{ \Carbon\Carbon::parse($comment['created_at'])->format('Y-m-d H:i') }})</small>
                                            <p>{{ $comment['comment'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No comments yet.</p>
                        @endif

                        <!-- Форма для нового коментаря -->
                        <form wire:submit.prevent="saveComment">
                            <div class="mb-3">
                                <label for="comment" class="form-label">New Comment</label>
                                <textarea wire:model="comment" class="form-control" id="comment" rows="3" placeholder="Enter your comment here"></textarea>
                                @error('comment')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                Save Comment
                            </button>
                        </form>
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

            window.addEventListener('open-comment-modal', function() {
                var modal = new bootstrap.Modal(document.getElementById('commentModal'));
                modal.show();
            });

            window.addEventListener('close-comment-modal', function() {
                var modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
                modal.hide();
            });
        </script>
    @endpush
</div>
