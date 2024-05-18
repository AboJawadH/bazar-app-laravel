<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalChannel;

class RatingCreatedNotification extends Notification implements ShouldQueue
{

    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $rating;
    public $post;


    public function __construct($rating, $post)
    {
        $this->rating = $rating;
        $this->post = $post;
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



    public function toOneSignal($notifiable)
    {

        $author = User::find($this->post->user_id);
        $authorLanguage = $author->locale;

        if ($authorLanguage === "ar") {
            return OneSignalMessage::create()
                ->setSubject("تقييم جديد")
                ->setBody("{$this->rating->user_name} قيم منشورك");
        } else if ($authorLanguage === "en") {
            return OneSignalMessage::create()
                ->setSubject("new rating")
                ->setBody("{$this->rating->user_name} left a rating on your post");
        } else {
            return OneSignalMessage::create()
                ->setSubject("yeni derecelendirme")
                ->setBody("{$this->rating->user_name} gönderinize puan bıraktı");
        }
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "تقييم جديد",
            'en_title' => "new rating",
            'tr_title' => "yeni derecelendirme",
            'body' => "{$this->rating->user_name}  ترك تقييما على منشورك",
            'en_body' => "{$this->rating->user_name}  left a rating on your post",
            'tr_body' => "{$this->rating->user_name}  gönderinize bir puan bıraktım",
            'post_id' => $this->rating->post_id,
            'user_id' => $this->rating->user_id,
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
