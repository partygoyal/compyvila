<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceived extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Submission $submission
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $competition = $this->submission->competition;
        $round = $this->submission->round;
        $submissionTitle = $this->submission->title ?: 'Untitled submission';

        return (new MailMessage)
            ->subject('Submission received')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your submission for {$competition->title} has been received.")
            ->line("Round: {$round->title}")
            ->line("Title: {$submissionTitle}")
            ->line('You will be notified when results or certificates are available.')
            ->salutation('Team CompyVila');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Submission received',
            'message' => "Your submission for {$this->submission->competition->title} has been received.",
            'url' => "/dashboard/submissions/{$this->submission->id}",
            'submission_id' => $this->submission->id,
            'competition_id' => $this->submission->competition_id,
            'competition_round_id' => $this->submission->competition_round_id,
            'status' => $this->submission->status,
        ];
    }
}
