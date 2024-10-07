<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Email;

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

        $emails = Email::where(function ($query) {
            $query->where('from_email', 'like', '%naeem043@gmail.com%')
                ->orWhere('from_email', 'like', '%aftabgirach@gmail.com%');
        })->where(function ($query) {
            $query->where('to_email', 'like', '%naeem043@gmail.com%')
                ->orWhere('to_email', 'like', '%aftabgirach@gmail.com%');
        })->get();

        $emailContent = $emails->map(function ($email) {
            return "Subject: {$email->subject}\nFrom: {$email->from_email}\nTo: {$email->to_email}\nContent: {$email->body_text}\n\n";
        })->implode("\n---\n");

        $completePrompt = "Here are the emails exchanged between Abu Nayem and Aftab Girach:\n\n" . $emailContent . "\n\nBased on these emails, " . $question;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',  // Or 'gpt-4', if available
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that analyzes emails to determine relationships and provide context.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $completePrompt
                    ]
                ],
                'max_tokens' => 500,
            ]);

            $responseBody = $response->json();

            if (isset($responseBody['choices'][0]['message']['content'])) {
                $chatResponse = $responseBody['choices'][0]['message']['content'];
            } else {
                $chatResponse = 'Sorry, I could not retrieve a response at this moment.';
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error connecting to the ChatGPT API: ' . $e->getMessage()]);
        }

        return view('chat', ['question' => $question, 'chatResponse' => $chatResponse]);
    }
}
