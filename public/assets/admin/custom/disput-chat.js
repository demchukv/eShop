export function initDisputChat(messagesUrl, sendMessageUrl, acceptUrl, contrproposalUrl, callAdminUrl) {
    $(document).ready(function () {
        // Function to display final decision
        function displayFinalDecision(response) {
            const finalDecisionDiv = $('#final-decision');
            const finalDecisionContent = $('#final-decision-content');
            if (!finalDecisionDiv.length || !finalDecisionContent.length) {
                return; // Exit if elements are not found
            }

            const currency = finalDecisionDiv.data('currency');
            const acceptedMessage = response.messages.find(msg => msg.proposal_status === 'accepted');

            if (acceptedMessage) {
                finalDecisionContent.html(`
                       <p class="mb-0"><strong>Refund Amount:</strong> ${currency}${parseFloat(acceptedMessage.refund_amount).toFixed(2)}</p>
                       <p class="mb-0"><strong>Application Type:</strong> ${response.application_types[acceptedMessage.application_type] || acceptedMessage.application_type}</p>
                       <p class="mb-0"><strong>Refund Method:</strong> ${response.refund_methods[acceptedMessage.refund_method] || acceptedMessage.refund_method}</p>
                       <p class="mb-0"><strong>Accepted:</strong> ${new Date(acceptedMessage.created_at).toLocaleString()}</p>
                   `);
            } else {
                finalDecisionContent.html('<p class="mb-0">Остаточне рішення відсутнє.</p>');
            }
        }

        // Load messages
        function loadMessages() {
            $.ajax({
                url: messagesUrl,
                method: 'GET',
                success: function (response) {
                    $('#chat-messages').empty();
                    response.messages.forEach(function (msg) {
                        var messageClass = msg.sender_type === 'admin' ? 'text-end' :
                            (msg.sender_type === 'seller' ? 'text-end' : 'text-start');
                        var messageBg = msg.sender_type === 'admin' ? 'bg-primary' :
                            (msg.sender_type === 'seller' ? 'bg-success' : 'bg-light');

                        var messageContent = '';
                        if (msg.proposal_status === 'open' || msg.proposal_status === 'counter') {
                            messageContent = `
                                   <p><strong>Proposed:</strong> ${msg.application_type ? response.application_types[msg.application_type] : ''}
                                   for ${response.currency}${parseFloat(msg.refund_amount).toFixed(2)}
                                   to ${msg.refund_method ? response.refund_methods[msg.refund_method] : ''}</p>
                               `;
                            if (msg.message) {
                                messageContent += `<p><strong>Message:</strong> ${msg.message}</p>`;
                            }
                            // Декодуємо evidence_path із JSON-рядка в масив
                            var evidencePaths = [];
                            if (msg.evidence_path) {
                                try {
                                    evidencePaths = typeof msg.evidence_path === 'string' ? JSON.parse(msg.evidence_path) : msg.evidence_path;
                                    if (!Array.isArray(evidencePaths)) {
                                        evidencePaths = [];
                                    }
                                } catch (e) {
                                    console.error('Error parsing evidence_path:', e, msg.evidence_path);
                                    evidencePaths = [];
                                }
                            }
                            if (evidencePaths.length > 0) {
                                messageContent += '<div class="evidence-gallery d-flex flex-wrap gap-2 mt-2">';
                                evidencePaths.forEach(function (path) {
                                    var extension = path.split('.').pop().toLowerCase();
                                    var isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(extension);
                                    var isVideo = ['mp4', 'webm', 'ogg'].includes(extension);
                                    if (isImage) {
                                        messageContent += `
                                               <a href="/storage/${path}" data-lightbox="evidence-${msg.id}">
                                                   <img src="/storage/${path}" alt="Evidence" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                               </a>`;
                                    } else if (isVideo) {
                                        messageContent += `
                                               <video width="200" controls>
                                                   <source src="/storage/${path}" type="video/${extension}">
                                                   Your browser does not support the video tag.
                                               </video>`;
                                    }
                                });
                                messageContent += '</div>';
                            }
                        } else if (msg.proposal_status === 'accepted') {
                            messageContent = '<p>Proposal accepted.</p>';
                        } else if (msg.proposal_status === 'admin_call') {
                            messageContent = '<p>Admin intervention requested.</p>';
                        } else if (msg.proposal_status === 'tracking_submitted') {
                            messageContent = `<p>${msg.message}</p>`;
                        } else if (msg.proposal_status === 'status_updated') {
                            messageContent = `<p>${msg.message}</p>`;
                        } else {
                            messageContent = `<p>${msg.message}</p>`;
                        }

                        var buttons = '';
                        if (msg.proposal_status === 'open' && response.can_respond && response.disput_status === 'open') {
                            buttons = `
                                   <div class="mt-2">
                                       <button type="button" class="btn btn-sm btn-success me-2 accept-btn" data-message-id="${msg.id}">Accept</button>
                                       <button type="button" class="btn btn-sm btn-primary me-2 contrproposal-btn"
                                           data-message-id="${msg.id}"
                                           data-refund-amount="${msg.refund_amount}"
                                           data-application-type="${msg.application_type}"
                                           data-refund-method="${msg.refund_method}">Contrproposal</button>
                                       <button type="button" class="btn btn-sm btn-warning call-admin-btn" data-message-id="${msg.id}">Call Admin</button>
                                   </div>`;
                        }

                        $('#chat-messages').append(
                            `<div class="mb-2 ${messageClass}">
                                   <div class="p-2 rounded d-inline-block ${messageBg}">
                                       <strong>${msg.sender_name}:</strong>
                                       ${messageContent}
                                       <small>${new Date(msg.created_at).toLocaleString()}</small>
                                       ${buttons}
                                   </div>
                               </div>`
                        );
                    });
                    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                    // Call displayFinalDecision after loading messages
                    displayFinalDecision(response);
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

        // Handle Accept
        $(document).on('click', '.accept-btn', function () {
            var messageId = $(this).data('message-id');
            $('#confirmAccept').data('message-id', messageId);
            $('#acceptModal').modal('show');
        });

        $('#confirmAccept').click(function () {
            var messageId = $(this).data('message-id');
            $.ajax({
                url: acceptUrl.replace(':messageId', messageId),
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    $('#acceptModal').modal('hide');
                    loadMessages();
                    alert(response.message);
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Handle Contrproposal
        $(document).on('click', '.contrproposal-btn', function () {
            var messageId = $(this).data('message-id');
            $('#contrproposalMessageId').val(messageId);
            $('#refundAmount').val($(this).data('refund-amount'));
            $('#applicationType').val($(this).data('application-type'));
            $('#refundMethod').val($(this).data('refund-method'));
            $('#contrproposalModal').modal('show');
        });

        $('#submitContrproposal').click(function () {
            var formData = new FormData($('#contrproposalForm')[0]);
            $.ajax({
                url: contrproposalUrl.replace(':messageId', $('#contrproposalMessageId').val()),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#contrproposalModal').modal('hide');
                    $('#contrproposalForm')[0].reset();
                    loadMessages();
                    alert(response.message);
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Handle Call Admin
        $(document).on('click', '.call-admin-btn', function () {
            var messageId = $(this).data('message-id');
            $('#confirmCallAdmin').data('message-id', messageId);
            $('#callAdminModal').modal('show');
        });

        $('#confirmCallAdmin').click(function () {
            var messageId = $(this).data('message-id');
            $.ajax({
                url: callAdminUrl.replace(':messageId', messageId),
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    $('#callAdminModal').modal('hide');
                    loadMessages();
                    alert(response.message);
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Refresh messages every 10 seconds
        setInterval(loadMessages, 10000);
    });
}