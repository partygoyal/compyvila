<?php

namespace App\Notifications;

use App\Models\CompetitionRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompetitionRegistrationConfirmed extends Notification
{
    use Queueable;

    public function __construct(
        private readonly CompetitionRegistration $registration
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $competition = $this->registration->competition;

        return (new MailMessage)
            ->subject('Competition registration received')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your registration for {$competition->title} has been received.")
            ->line("Registration number: {$this->registration->registration_number}")
            ->line("Status: {$this->registration->status}")
            ->line('We will keep you updated as the competition progresses.')
            ->salutation('Team CompyVila');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Registration confirmed',
            'message' => "Your registration for {$this->registration->competition->title} has been received.",
            'url' => '/dashboard/registrations',
            'registration_id' => $this->registration->id,
            'competition_id' => $this->registration->competition_id,
            'registration_number' => $this->registration->registration_number,
            'status' => $this->registration->status,
        ];
    }
}
