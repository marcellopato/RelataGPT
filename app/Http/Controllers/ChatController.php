<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessEmailsChunkJob;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    // Step 1: Handle file import
    // Step 1: Handle file import
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'json_file' => 'required|file|mimes:json'
        ]);

        // Read the uploaded JSON file
        $jsonFile = $request->file('json_file');
        $jsonData = json_decode(file_get_contents($jsonFile->getRealPath()), true);

        if (!is_null($jsonData)) {
            // Process JSON data in chunks
            $chunkSize = 500; // Set the chunk size (e.g., 500 emails per chunk)
            $chunks = array_chunk($jsonData, $chunkSize);

            foreach ($chunks as $chunk) {
                // Dispatch each chunk to a background job for processing
                ProcessEmailsChunkJob::dispatch($chunk);
            }
        }

        return response()->json(['message' => 'File is being processed.']);
    }


    // Step 2: Handle the question form
    public function ask(Request $request)
    {
        // Validate the question
        $request->validate([
            'question' => 'required|string'
        ]);

        $question = $request->input('question');

        // Fetch relevant emails from the database
        $emails = Email::where(function ($query) {
            $query->where('from_email', 'like', '%naeem043@gmail.com%')
                ->orWhere('from_email', 'like', '%aftabgirach@gmail.com%');
        })->where(function ($query) {
            $query->where('to_email', 'like', '%naeem043@gmail.com%')
                ->orWhere('to_email', 'like', '%aftabgirach@gmail.com%');
        })->get();

        // Format email content for ChatGPT prompt
        $emailContent = $emails->map(function ($email) {
            return "Subject: {$email->subject}\nFrom: {$email->from_email}\nTo: {$email->to_email}\nContent: {$email->body_text}\n\n";
        })->implode("\n---\n");

        $completePrompt = "Here are the emails exchanged between Abu Nayem and Aftab Girach:\n\n" . $emailContent . "\n\nBased on these emails, " . $question;

        try {
            // Send request to OpenAI API
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
                        'content' => $completePrompt
                    ]
                ],
                'max_tokens' => 500,
            ]);

            $responseBody = $response->json();

            // Check if a valid response from ChatGPT is received
            if (isset($responseBody['choices'][0]['message']['content'])) {
                $chatResponse = $responseBody['choices'][0]['message']['content'];
            } else {
                $chatResponse = 'Sorry, I could not retrieve a response at this moment.';
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error connecting to the ChatGPT API: ' . $e->getMessage()], 500);
        }

        return response()->json(['response' => $chatResponse]);
    }

}
