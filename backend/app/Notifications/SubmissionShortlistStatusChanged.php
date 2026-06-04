<?php

namespace App\Notifications;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionShortlistStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Submission $submission,
        private readonly bool $shortlisted
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $competition = $this->submission->competition;
        $status = $this->shortlisted
            ? 'shortlisted and promoted to the next round if one is available'
            : 'removed from the shortlist';

        return (new MailMessage)
            ->subject('Submission shortlist update')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your submission for {$competition->title} has been {$status}.")
            ->line('Open your dashboard for the latest status.')
            ->salutation('Team CompyVila');
    }

    public function toArray(object $notifiable): array
    {
        $nextRound = $this->nextRound();

        return [
            'title' => $this->shortlisted
                ? 'Submission shortlisted'
                : 'Shortlist status updated',
            'message' => $this->shortlisted
                ? $this->shortlistedMessage($nextRound)
                : 'Your submission was removed from the shortlist.',
            'url' => "/dashboard/submissions/{$this->submission->id}",
            'submission_id' => $this->submission->id,
            'next_round_id' => $nextRound?->id,
            'next_round_number' => $nextRound?->round_number,
            'status' => $this->submission->status,
        ];
    }

    private function shortlistedMessage($nextRound): string
    {
        if (! $nextRound) {
            return 'Your submission has been shortlisted in the final round.';
        }

        return "Your submission has been shortlisted. You are promoted to Round {$nextRound->round_number}.";
    }

    private function nextRound()
    {
        if (! $this->shortlisted || ! $this->submission->round) {
            return null;
        }

        return $this->submission->competition
            ->rounds()
            ->where('round_number', '>', $this->submission->round->round_number)
            ->orderBy('round_number')
            ->first();
    }
}
