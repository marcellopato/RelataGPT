<?php

namespace App\Jobs;

use App\Models\ChatResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessChatRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chatResponseId;

    public function __construct($chatResponseId)
    {
        $this->chatResponseId = $chatResponseId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find the chat response record in the database
        $chatResponse = ChatResponse::find($this->chatResponseId);

        if (!$chatResponse) {
            // If the chat response record is not found, do nothing
            return;
        }

        try {
            // Make a request to the OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    // Define the system message
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that analyzes emails to determine relationships and provide context.'
                    ],
                    // Define the user message
                    [
                        'role' => 'user',
                        'content' => $chatResponse->question
                    ]
                ],
                'max_tokens' => 500,
            ]);

            // Extract the response content from the API
            $responseBody = $response->json();
            $chatResponseContent = $responseBody['choices'][0]['message']['content'] ?? 'No response available.';

            // Save the response to the database
            $chatResponse->response = $chatResponseContent;
            $chatResponse->is_processed = true; // Indicate that the processing is complete
            $chatResponse->save();
        } catch (\Exception $e) {
            // Handle failures
        }
    }
}

