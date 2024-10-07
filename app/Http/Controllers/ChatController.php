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
            // Requisição para a API do OpenAI usando o modelo gpt-3.5-turbo
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',  // Ou 'gpt-4', se estiver habilitado
                'messages' => [
                    [
                        'role' => 'system', // Define o comportamento do ChatGPT
                        'content' => 'Você é um assistente útil que analisa a relação entre pessoas baseadas em emails.'
                    ],
                    [
                        'role' => 'user', // Mensagem do usuário
                        'content' => $question
                    ]
                ],
                'max_tokens' => 150,
            ]);

            $responseBody = $response->json();

            // Verifique se a chave 'choices' existe e se contém o texto da resposta
            if (isset($responseBody['choices']) && isset($responseBody['choices'][0]['message']['content'])) {
                $chatResponse = $responseBody['choices'][0]['message']['content'];
            } else {
                $chatResponse = 'Desculpe, não foi possível obter uma resposta no momento.';
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao conectar-se à API do ChatGPT: ' . $e->getMessage()]);
        }

        return view('chat', ['question' => $question, 'chatResponse' => $chatResponse]);
    }
}
