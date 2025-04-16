<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Disput;

class DisputAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $disput;

    public function __construct(Disput $disput)
    {
        $this->disput = $disput;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Admin Intervention Requested for Disput #' . $this->disput->id)
            ->line('A disput requires your attention.')
            ->line('Disput ID: ' . $this->disput->id)
            ->action('View Disput', route('admin.disput.show', $this->disput->id))
            ->line('Thank you for your assistance!');
    }

    public function toArray($notifiable)
    {
        return [
            'disput_id' => $this->disput->id,
            'message' => 'Admin intervention requested for disput #' . $this->disput->id,
            'url' => route('admin.disput.show', $this->disput->id),
        ];
    }
}
