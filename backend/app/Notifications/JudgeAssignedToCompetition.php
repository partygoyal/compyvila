<?php

namespace App\Notifications;

use App\Models\JudgeAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JudgeAssignedToCompetition extends Notification
{
    use Queueable;

    public function __construct(
        private readonly JudgeAssignment $assignment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New judging assignment')
            ->greeting("Hi {$notifiable->name},")
            ->line("You have been assigned to judge {$this->assignment->competition->title}.")
            ->line("Round: {$this->assignment->round->title}")
            ->line('Open your judge dashboard to review assigned submissions.')
            ->salutation('Team CompyVila');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New judging assignment',
            'message' => "You have been assigned to {$this->assignment->competition->title} - {$this->assignment->round->title}.",
            'url' => '/dashboard/judge',
            'assignment_id' => $this->assignment->id,
            'competition_id' => $this->assignment->competition_id,
            'competition_round_id' => $this->assignment->competition_round_id,
        ];
    }
}
