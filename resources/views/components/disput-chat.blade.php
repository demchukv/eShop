<div class="disput-chat">
    <h5>{{ labels('admin_labels.chat_history', 'Chat History') }}</h5>
    <div class="chat-container" id="chat-messages"
        style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <!-- Messages will be loaded here -->
    </div>
    @if ($disput->status === 'open')
        <div class="chat-input mt-3">
            <form id="send-message-form">
                @csrf
                <div class="input-group">
                    <input type="hidden" name="disput_id" value="{{ $disput->id }}">
                    <textarea name="message" class="form-control"
                        placeholder="{{ labels('admin_labels.type_message', 'Type your message...') }}" required></textarea>
                    <button type="submit" class="btn btn-primary">{{ labels('admin_labels.send', 'Send') }}</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Модалка для Accept -->
    <div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acceptModalLabel">Confirm Acceptance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to accept this proposal?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAccept">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модалка для Contrproposal -->
    <div class="modal fade" id="contrproposalModal" tabindex="-1" aria-labelledby="contrproposalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contrproposalModalLabel">Make a Counterproposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contrproposalForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="message_id" id="contrproposalMessageId">
                        <div class="mb-3">
                            <label for="refundAmount" class="form-label">Refund Amount</label>
                            <input type="number" class="form-control" id="refundAmount" name="refund_amount"
                                step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicationType" class="form-label">Application Type</label>
                            <select class="form-select" id="applicationType" name="application_type" required>
                                <option value="">Select an option...</option>
                                @foreach (config('application_types') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="refundMethod" class="form-label">Refund Method</label>
                            <select class="form-select" id="refundMethod" name="refund_method" required>
                                <option value="">Select an option...</option>
                                @foreach (config('refund_methods') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="evidence" class="form-label">Upload Evidence</label>
                            <input type="file" class="form-control" id="evidence" name="evidence[]" multiple
                                accept="image/*,video/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitContrproposal">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модалка для Call Admin -->
    <div class="modal fade" id="callAdminModal" tabindex="-1" aria-labelledby="callAdminModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="callAdminModalLabel">Request Admin Intervention</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to request admin intervention?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmCallAdmin">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
