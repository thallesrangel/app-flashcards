<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatIaController extends Controller
{
    public function index()
    {
        return view('chat-ia.chat');
    }

    public function talkIa(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 0.2;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $conversation_mode = "";

        if(isset($request->conversation_mode)) {
            $conversation_mode = "O modo da conversa é :" . $request->conversation_mode;
        }
        
        $prompt = <<<EOT
        Você é um professor de inglês amigável e experiente. Sua tarefa é manter uma conversa construtiva em inglês com alunos que estão praticando o idioma. 
        $conversation_mode
        Sempre responda de forma clara, educacional e encorajadora. 
        Corrija erros sutilmente quando necessário, e estimule o aluno a continuar conversando, fazendo novas perguntas ou comentários. 
        Evite traduzir para o português — mantenha toda a conversa em inglês. 
        Seja gentil, paciente e ajude o aluno a desenvolver confiança.
        EOT;
        

        $userMessage = "Conversa: {$request->content}";

        $data = [
            'model' => $model,
            'temperature' => $temperature,
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $userMessage]
            ]
        ];
        
        $client = new Client();

        try {
            $response = $client->post($apiUrl, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ]
            ]);


            $res = $response->getBody()->getContents();

            $responseData = json_decode($res, true);

            $rawContent = $responseData['choices'][0]['message']['content'] ?? '';

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'content' => $rawContent,
                    'usage' => $responseData['usage'] ?? '',
                    'model' => $responseData['model'] ?? ''
                ];
            } else {
                return [
                    'error' => 'Erro ao interpretar resposta da IA.',
                    'raw' => $rawContent
                ];
            }

        } catch (\Exception $e) {
            return [
                'error' => 'Erro na requisição: ' . $e->getMessage()
            ];
        }

    }
}