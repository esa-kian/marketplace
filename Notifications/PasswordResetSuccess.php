<?php

namespace App\Notifications;

use App\Mail\SuccessPasswordResetMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetSuccess extends Notification implements ShouldQueue
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
        $details = [
            'title' => 'You changed your password successful.',
            'body' => "If you did not change password, protect your account."
        ];

        Mail::to($this->passwordReset->email)->send(new SuccessPasswordResetMail($details));

        return (new SuccessPasswordResetMail($details));
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
