<div class="disput-chat">
    <h5>{{ labels('admin_labels.chat_history', 'Chat History') }}</h5>
    <div class="chat-container" id="chat-messages"
        style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <!-- Messages will be loaded here -->
    </div>
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
</div>
