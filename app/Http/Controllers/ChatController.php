<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessChatRequest;
use App\Models\ChatResponse;

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

        // Create a new response record
        $chatResponse = ChatResponse::create([
            'question' => $question,
        ]);

        // Dispatch the job to process the question in the background
        ProcessChatRequest::dispatch($chatResponse->id);

        // Return the ID of the chatResponse for the frontend to make the polling
        return response()->json([
            'status' => 'processing',
            'chatResponseId' => $chatResponse->id,
            'message' => 'Your request is being processed.'
        ]);
    }

    public function getChatResponse($id)
    {
        $chatResponse = ChatResponse::find($id);

        if (!$chatResponse) {
            return response()->json(['error' => 'Chat response not found'], 404);
        }

        return response()->json([
            'response' => $chatResponse->response,
            'is_processed' => $chatResponse->is_processed,
        ]);
    }
}
