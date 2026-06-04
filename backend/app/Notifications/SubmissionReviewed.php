<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReviewed extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Review $review
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $submission = $this->review->submission;
        $competition = $submission->competition;

        return (new MailMessage)
            ->subject('Your submission has been reviewed')
            ->greeting("Hi {$notifiable->name},")
            ->line("A judge has reviewed your submission for {$competition->title}.")
            ->line("Total score: {$this->review->total_score}/40")
            ->line('Open your dashboard to see the judge-wise score breakdown.')
            ->salutation('Team CompyVila');
    }

    public function toArray(object $notifiable): array
    {
        $submission = $this->review->submission;

        return [
            'title' => 'Submission reviewed',
            'message' => "{$this->review->judge->name} scored your submission {$this->review->total_score}/40.",
            'url' => "/dashboard/submissions/{$submission->id}",
            'submission_id' => $submission->id,
            'review_id' => $this->review->id,
            'judge_id' => $this->review->judge_id,
            'judge_name' => $this->review->judge->name,
            'total_score' => $this->review->total_score,
        ];
    }
}
