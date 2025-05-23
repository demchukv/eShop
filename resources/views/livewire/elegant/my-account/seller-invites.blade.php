<div>
    <div class="container">

        <button wire:click="createInvite" class="btn-secondary mb-3">
            Create an invitation
        </button>

        @if (!$invites || $invites->isEmpty())
            <p>You haven't created any invitations yet or they are out of date.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Link</th>
                        <th>Copy</th>
                        <th>Status</th>
                        <th>Creation date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invites as $invite)
                        <tr wire:key="invite-{{ $invite->id }}">
                            <td>{{ $invite->id }}</td>
                            <td>{{ config('app.url') . 'seller-register/' . $invite->link }}</td>
                            <td>
                                @if ($invite->status === \App\Models\SellerInvite::STATUS_ACTIVE)
                                    <livewire:components.copy-button :text="config('app.url') . 'seller-register/' . $invite->link"
                                        :wire:key="'copy-' . $invite->id" />
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($invite->status == 'active')
                                    <span class="badge text-bg-primary">{{ $invite->status }}</span>
                                @endif
                                @if ($invite->status == 'used')
                                    <span class="badge text-bg-success">{{ $invite->status }}</span>
                                @endif
                                @if ($invite->status == 'expired')
                                    <span class="badge text-bg-secondary">{{ $invite->status }}</span>
                                @endif
                            </td>
                            <td>{{ $invite->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <button wire:click="deleteInvite({{ $invite->id }})" class="btn-link btn-lg p-0"
                                    onclick="return confirm('Delete?')" title="Delete link">
                                    <ion-icon name="trash-outline" role="img" class="md hydrated"></ion-icon>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                @if ($invites instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $invites->links() }}
                @endif
            </div>
        @endif
    </div>

    @script
        <script>
            document.addEventListener('livewire:init', function() {
                // Обробка успішних повідомлень
                window.addEventListener('show-success-invite', function(event) {
                    iziToast.success({
                        message: event.detail.message,
                        position: 'topRight'
                    });
                });

                // Обробка помилок
                window.addEventListener('show-error-invite', function(event) {
                    iziToast.error({
                        message: event.detail.message,
                        position: 'topRight'
                    });
                });
            });
        </script>
    @endscript
</div>
