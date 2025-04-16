// resources/js/disput-chat.js
export function initDisputChat(messagesUrl, sendMessageUrl) {
    $(document).ready(function () {
        // Load messages
        function loadMessages() {
            $.ajax({
                url: messagesUrl,
                method: 'GET',
                success: function (response) {
                    $('#chat-messages').empty();
                    response.messages.forEach(function (msg) {
                        var messageClass = msg.sender_type === 'admin' || msg.sender_type === 'seller' ? 'text-end' : 'text-start';
                        var messageBg = msg.sender_type === 'admin' ? 'bg-primary' :
                            (msg.sender_type === 'seller' ? 'bg-success' : 'bg-light');
                        $('#chat-messages').append(
                            '<div class="mb-2 ' + messageClass + '">' +
                            '<div class="p-2 rounded d-inline-block ' + messageBg + '">' +
                            '<strong>' + msg.sender_name + ':</strong> ' + msg.message + '<br>' +
                            '<small>' + new Date(msg.created_at).toLocaleString() + '</small>' +
                            '</div></div>'
                        );
                    });
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                }
            });
        }

        // Initial load
        loadMessages();

        // Send message
        $('#send-message-form').submit(function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: sendMessageUrl,
                method: 'POST',
                data: formData,
                success: function (response) {
                    $('#send-message-form')[0].reset();
                    loadMessages();
                },
                error: function (xhr) {
                    alert('Error sending message: ' + xhr.responseJSON.message);
                }
            });
        });

        // Refresh messages every 10 seconds
        setInterval(loadMessages, 10000);
    });
}