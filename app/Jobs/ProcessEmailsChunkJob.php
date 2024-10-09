<?php

namespace App\Jobs;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEmailsChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chunk;

    /**
     * Create a new job instance.
     *
     * @param array $chunk
     */
    public function __construct(array $chunk)
    {
        $this->chunk = $chunk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emails = [];

        foreach ($this->chunk as $emailData) {
            // Prepare each email record for bulk insertion
            $emails[] = [
                'subject' => $emailData['subject'] ?? 'No subject',
                'from_email' => $emailData['from'][0] ?? 'unknown',
                'to_email' => $emailData['to'][0] ?? 'unknown',
                'body_text' => $emailData['body']['text'] ?? null,
                'body_html' => $emailData['body']['html'] ?? null,
                'email_date' => !empty($emailData['date']) ? $emailData['date'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Perform a bulk insert
        Email::insert($emails);
    }
}
