@php
    $bread_crumb['page_main_bread_crumb'] = labels('front_messages.support', 'Support');
    $bread_crumb['page_sub_bread_crumb'] = labels('front_messages.ticket_details', 'Ticket Details');
@endphp

<div id="page-content">
    <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
    <div class="container-fluid">
        <div class="row">
            <x-utility.my_account_slider.account_slider :$user_info />
            <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                <div class="dashboard-content h-100">
                    <div class="h-100" id="ticket-details">
                        <h2 class="mb-4">{{ labels('front_messages.ticket_details', 'Ticket Details') }}
                            #{{ $ticket->id }}</h2>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>{{ $ticket->subject }}</h5>
                                <p><strong>{{ labels('front_messages.status', 'Status') }}:</strong>
                                    @if ($ticket->status == 0)
                                        {{ labels('front_messages.in_review', 'In Review') }}
                                    @elseif ($ticket->status == 2)
                                        {{ labels('front_messages.opened', 'Opened') }}
                                    @elseif ($ticket->status == 3)
                                        {{ labels('front_messages.resolved', 'Resolved') }}
                                    @elseif ($ticket->status == 4)
                                        {{ labels('front_messages.closed', 'Closed') }}
                                    @elseif ($ticket->status == 5)
                                        {{ labels('front_messages.reopened', 'Reopened') }}
                                    @endif
                                </p>
                                <p><strong>{{ labels('front_messages.created_date', 'Created Date') }}:</strong>
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y') }}</p>
                                <p><strong>{{ labels('front_messages.updated_date', 'Updated Date') }}:</strong>
                                    {{ \Carbon\Carbon::parse($ticket->updated_at)->format('d-m-Y') }}</p>
                                <p><strong>{{ labels('front_messages.description', 'Description') }}:</strong>
                                    {{ $ticket->description }}</p>
                            </div>
                        </div>

                        <h4>{{ labels('front_messages.messages', 'Messages') }}</h4>
                        <div class="card direct-chat direct-chat-primary mb-4">
                            <div class="card-body">
                                <div class="direct-chat-messages" style="height: 400px; overflow-y: auto;">
                                    @foreach ($messages as $message)
                                        <div
                                            class="direct-chat-msg mb-3 {{ $message->user_type == 'user' ? 'right' : '' }}">
                                            <div class="direct-chat-infos clearfix">
                                                <span
                                                    class="direct-chat-name {{ $message->user_type == 'user' ? 'float-right' : 'float-left' }}">
                                                    {{ $message->user_type == 'user' ? $user_info->username : 'Support' }}
                                                </span>
                                                <span
                                                    class="direct-chat-timestamp {{ $message->user_type == 'user' ? 'float-left' : 'float-right' }}">
                                                    {{ \Carbon\Carbon::parse($message->created_at)->format('d-m-Y H:i') }}
                                                </span>
                                            </div>
                                            <div
                                                class="direct-chat-text p-2 {{ $message->user_type == 'user' ? 'bg-secondary text-white' : 'bg-light' }}">
                                                {{ $message->message }}
                                            </div>
                                            @if (!empty($message->processed_attachments))
                                                <div class="direct-chat-attachments mt-2">
                                                    @foreach ($message->processed_attachments as $attachment)
                                                        @if ($attachment['type'] == 'image')
                                                            <a href="{{ $attachment['media'] }}" target="_blank">
                                                                <img src="{{ $attachment['media'] }}" alt="Attachment"
                                                                    style="max-width: 100px;" class="me-2">
                                                            </a>
                                                        @elseif ($attachment['type'] == 'video')
                                                            <a href="{{ $attachment['media'] }}" target="_blank">Video
                                                                Attachment</a>
                                                        @elseif ($attachment['type'] == 'document' || $attachment['type'] == 'archive')
                                                            <a href="{{ $attachment['media'] }}"
                                                                target="_blank">Download {{ $attachment['type'] }}</a>
                                                        @else
                                                            <a href="{{ $attachment['media'] }}"
                                                                target="_blank">Download File</a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if (empty($messages))
                                        <p class="text-center">
                                            {{ labels('front_messages.no_messages', 'No messages yet.') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <form wire:submit="addMessage" enctype="multipart/form-data">
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="message" name="message"
                                            placeholder="Type Message ..." class="form-control">
                                        <span class="input-group-append">
                                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="addMessage">Send</span>
                                                <span wire:loading wire:target="addMessage">Sending...</span>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="input-group">
                                        <input type="file" wire:model="files" name="files[]" multiple
                                            class="form-control" wire:loading.attr="disabled">
                                        <div wire:loading wire:target="files">
                                            Uploading...
                                        </div>
                                    </div>
                                    @error('message')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('files.*')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
