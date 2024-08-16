<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use App\Models\User;

class ChatMessageCreatedNotification extends Notification
{
    use Queueable;


    public $messageSender;
    public $chatId;


    /**
     * Create a new notification instance.
     */
    public function __construct($messageSender,$chatId)
    {
        $this->messageSender = $messageSender;
        $this->chatId = $chatId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'database',
            OneSignalChannel::class
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toOneSignal($notifiable)
    {

        // $currentLocale = App::getLocale();
        // Log::debug($currentLocale);
        // $author = User::find($this->post->user_id);
        $authorLanguage = $this->messageSender->locale;

        if ($authorLanguage === "ar") {
            return OneSignalMessage::create()
                ->setSubject("رسالة جديدة")
                ->setBody("{$this->messageSender->name} أرسل لك رسالة");
        } else if ($authorLanguage === "en") {
            return OneSignalMessage::create()
                ->setSubject("new message")
                ->setBody("{$this->messageSender->name} left a message for you");
        } else {
            return OneSignalMessage::create()
                ->setSubject("yeni mesaj")
                ->setBody("{$this->messageSender->name} sana bir mesaj bıraktı");
        }
    }


    public function toDatabase($notifiable)
    {
        return [
            'chat_id' => $this->chatId,
            'title' => "رسالة جديدة",
            'en_title' => "new message",
            'tr_title' => "yeni mesaj",
            'body' => "{$this->messageSender->name} أرسل لك رسالة",
            'en_body' => "{$this->messageSender->name} left a message for you",
            'tr_body' => "{$this->messageSender->name} sana bir mesaj bıraktı",
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
