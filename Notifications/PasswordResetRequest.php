<?php

namespace App\Notifications;

use App\Mail\PasswordResetMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class PasswordResetRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $passwordReset;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($passwordReset)
    {
        $this->passwordReset = $passwordReset;
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
        $url = url('/api/password/find/' . $this->passwordReset->token);

        $details = [
            'title' => 'You are receiving this email because we received a password reset request for your account.',
            'body' => "Click on below link to reset your password" . "(If you did not request a password reset, no further action is required.)",
            'url' =>url($url),
            'email' => $this->passwordReset->email

        ];

        Mail::to($this->passwordReset->email)->send(new PasswordResetMail($details));

        return (new PasswordResetMail($details));
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
