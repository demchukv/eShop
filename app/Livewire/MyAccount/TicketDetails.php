<?php

namespace App\Livewire\MyAccount;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TicketDetails extends Component
{
    use WithFileUploads;

    public $ticketId;
    public $message = '';
    public $files = [];

    public function mount($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function render()
    {
        $user = Auth::user() ?? null;
        $ticket = fetchDetails('tickets', ['id' => $this->ticketId, 'user_id' => $user->id], "*", "", "", "tickets.id", "DESC");
        $ticket = !empty($ticket) ? $ticket[0] : null;

        if (!$ticket) {
            abort(404, 'Ticket not found or you do not have access.');
        }

        $messages = fetchDetails('ticket_messages', ['ticket_id' => $this->ticketId], "*", "", "", "created_at", "ASC");

        // Process attachments for display
        $typeConfig = config('eshop_pro.type');
        $messages = collect($messages)->map(function ($message) use ($typeConfig) {
            $message->attachments = !empty($message->attachments) && $message->attachments != 'null'
                ? json_decode($message->attachments, true)
                : [];
            $message->processed_attachments = array_map(function ($attachment) use ($typeConfig) {
                $file = new \SplFileInfo($attachment);
                $ext = $file->getExtension();
                $mediaType = 'unknown';
                if (in_array($ext, $typeConfig['image']['types'])) {
                    $mediaType = 'image';
                } elseif (in_array($ext, $typeConfig['video']['types'])) {
                    $mediaType = 'video';
                } elseif (in_array($ext, $typeConfig['document']['types'])) {
                    $mediaType = 'document';
                } elseif (in_array($ext, $typeConfig['archive']['types'])) {
                    $mediaType = 'archive';
                }
                return [
                    'media' => getMediaImageUrl($attachment),
                    'type' => $mediaType,
                ];
            }, $message->attachments);
            return $message;
        })->all();

        return view('livewire.' . config('constants.theme') . '.my-account.ticket-details', [
            'user_info' => $user,
            'ticket' => $ticket,
            'messages' => $messages,
        ])->title("Ticket #{$ticket->id} |");
    }

    public function addMessage()
    {
        try {
            $validator = Validator::make(
                ['message' => $this->message, 'files' => $this->files],
                [
                    'message' => 'required|string|max:5000',
                    'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,zip,rar|max:10240',
                ]
            );

            if ($validator->fails()) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => implode(', ', $validator->errors()->all())
                ]);
                return;
            }

            $user_id = Auth::user()->id;
            $ticket = Ticket::find($this->ticketId);

            if (!$ticket || $ticket->user_id != $user_id) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Invalid ticket or unauthorized access.'
                ]);
                return;
            }

            $attachments = [];
            if (!empty($this->files)) {
                foreach ($this->files as $file) {
                    try {
                        $filePath = $file->store('ticket_files', 'public');
                        $attachments[] = $filePath;
                    } catch (\Exception $e) {
                        Log::error('Error storing file', [
                            'error' => $e->getMessage(),
                            'file' => $file->getClientOriginalName()
                        ]);
                    }
                }
            }

            $message = new TicketMessage([
                'ticket_id' => $this->ticketId,
                'user_id' => $user_id,
                'user_type' => 'user',
                'message' => $this->message,
                'attachments' => json_encode($attachments),
            ]);

            $result = $message->save();

            if (!$result) {
                Log::error('Failed to save ticket message', [
                    'ticket_id' => $this->ticketId,
                    'user_id' => $user_id
                ]);
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Failed to send message. Please try again.'
                ]);
                return;
            }

            $this->message = '';
            $this->files = [];

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Message sent successfully.'
            ]);

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Log::error('Error in addMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'An unexpected error occurred. Please try again.'
            ]);
        }
    }

    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }

    public function updatedFiles()
    {
        try {
            if (empty($this->files)) {
                Log::info('No files to process');
                return;
            }

            // Валідація файлів
            $validator = Validator::make(
                ['files' => $this->files],
                ['files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,zip,rar|max:10240']
            );

            if ($validator->fails()) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => implode(', ', $validator->errors()->all())
                ]);
                $this->files = [];
                return;
            }

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'File uploaded seccessfully!'
            ]);

            // Оновлюємо компонент
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Log::error('Error in updatedFiles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Error uploading files'
            ]);

            $this->files = [];
        }
    }

    public function handleFileUpload()
    {
        try {
            // Валідація файлів
            $validator = Validator::make(
                ['files' => $this->files],
                ['files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,zip,rar|max:10240']
            );

            if ($validator->fails()) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => implode(', ', $validator->errors()->all())
                ]);
                return;
            }

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'File uploaded seccessfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in handleFileUpload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Error uploading files'
            ]);
        }
    }
}
