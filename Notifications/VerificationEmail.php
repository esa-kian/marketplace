<?php

namespace App\Notifications;

use App\Mail\VerifyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class VerificationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $email;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/api/verification/' . $this->token);

        $details = [
            'title' => 'Thanks for joining SECUREPATH! We really appreciate it. Please click the button below to verify your account:',
            'body' => "If you did not create an account, no further action is required.",
            'url' =>url($url),
            'email' => $this->email

        ];

        Mail::to($this->email)->send(new VerifyMail($details));

        return (new VerifyMail($details));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
