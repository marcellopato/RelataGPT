<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
        ]);

        $question = $request->input('question');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that analyzes the relationships between people based on their emails.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'max_tokens' => 150,
            ]);

            $responseBody = $response->json();

            if (isset($responseBody['choices'][0]['message']['content'])) {
                $chatResponse = $responseBody['choices'][0]['message']['content'];
            } else {
                $chatResponse = 'Sorry, I was unable to get an answer at the moment.';
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error connecting to ChatGPT API: ' . $e->getMessage()]);
        }

        return view('chat', ['question' => $question, 'chatResponse' => $chatResponse]);
    }
}
