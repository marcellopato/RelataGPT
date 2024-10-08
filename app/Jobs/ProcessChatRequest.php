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

    public function handle()
    {
        $chatResponse = ChatResponse::find($this->chatResponseId);

        if (!$chatResponse) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that analyzes emails to determine relationships and provide context.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $chatResponse->question
                    ]
                ],
                'max_tokens' => 500,
            ]);

            $responseBody = $response->json();
            $chatResponseContent = $responseBody['choices'][0]['message']['content'] ?? 'No response available.';

            // Salvar a resposta no banco de dados
            $chatResponse->response = $chatResponseContent;
            $chatResponse->is_processed = true; // Indicar que o processamento foi concluído
            $chatResponse->save();
        } catch (\Exception $e) {
            // Lidar com falhas
        }
    }
}
