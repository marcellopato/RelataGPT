<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Email;
use Illuminate\Support\Facades\File;


class ImportEmailsFromJson extends Command
{
    protected $signature = 'import:emails {file}'; // Pass the file as argument

    protected $description = 'Import emails from a JSON file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("File not found: " . $filePath);
            return 1;
        }

        $jsonData = File::get($filePath);
        $emails = json_decode($jsonData, true);

        foreach ($emails as $emailData) {
            Email::create([
                'subject' => $emailData['subject'] ?? 'No subject',
                'from_email' => $emailData['from'][0] ?? 'unknown',
                'to_email' => $emailData['to'][0] ?? 'unknown',
                'body_text' => $emailData['body']['text'] ?? null,
                'body_html' => $emailData['body']['html'] ?? null,
                'email_date' => isset($emailData['date_in_ms']) ? date('Y-m-d H:i:s', $emailData['date_in_ms'] / 1000) : null,
            ]);
        }

        $this->info("Emails imported successfully.");
        return 0;
    }
}
