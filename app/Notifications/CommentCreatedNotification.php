<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use Berkayk\OneSignal\Facades\OneSignal;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CommentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $post;


    public function __construct($comment, $post)
    {
        $this->comment = $comment;
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

    /**
     * Get the data for the OneSignal channel.
     *
     * @param object $notifiable
     * @return OneSignalMessage
     */


    public function toOneSignal($notifiable)
    {

        // $currentLocale = App::getLocale();
        // Log::debug($currentLocale);

        return OneSignalMessage::create()
            ->setSubject("new comment")
            ->setBody("{$this->comment->user_name}  left a comment on your post");
        // ->setData('title', "تعليق جديد") // Key and value for title
        // ->setData('body', "{$this->comment->user_name}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "تعليق جديد",
            'en_title' => "new comment",
            'tr_title' => "yeni yorum",
            'body' => "{$this->comment->user_name}  ترك تعليقا على منشورك",
            'en_body' => "{$this->comment->user_name}  left a comment on your post",
            'tr_body' => "{$this->comment->user_name}  gönderinize yorum bıraktı",
            'post_id' => $this->comment->post_id,
            'user_id' => $this->comment->user_id,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     // ... (Optional email notification logic)
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // ... (Optional database notification data)
        ];
    }
}
