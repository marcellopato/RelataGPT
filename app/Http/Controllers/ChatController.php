<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Email;
use App\Jobs\ProcessChatRequest;
use Illuminate\Support\Facades\Cache;
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

        // Criar um novo registro de resposta
        $chatResponse = ChatResponse::create([
            'question' => $question,
        ]);

        // Despachar o job para processar a pergunta em segundo plano
        ProcessChatRequest::dispatch($chatResponse->id);

        // Retornar o ID do chatResponse para o frontend fazer o polling
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
