<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compositions;
use GuzzleHttp\Client;

class CompositionController extends Controller
{
    protected $compositionModel;

    public function __construct(Compositions $compositionModel)
    {
        $this->compositionModel = $compositionModel;
    }

    public function index()
    {
        return view('composition.index');
    }

    public function checkText(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $personality = $request->ai_personality;

        $prompt = <<<EOT
        Você é um professor de inglês com uma personalidade {$personality}. Sua tarefa é corrigir a redação enviada pelo aluno, mantendo a correção em **inglês**, e fornecer um **feedback separado em português**.

        - A correção deve ser clara e manter o estilo do aluno.
        - O feedback deve ser breve e construtivo (somente se necessário), explicando o que o aluno pode melhorar.
        - Avalie os seguintes critérios: **cohesion**, **coherence**, **grammar** e **vocabulary**.

        Retorne exatamente neste formato JSON:
        {
        "corrigido": "<redação corrigida em inglês>",
        "feedback": "<comentário construtivo e breve em português, se necessário>",
        "cohesion": "<comentário sobre a coesão em inglês>",
        "coherence": "<comentário sobre a coerência em inglês>",
        "grammar": "<comentário sobre a gramática em inglês>",
        "vocabulary": "<comentário sobre o vocabulário em inglês>"
        }
        EOT;
        
        $userMessage = "Texto: {$request->content}";

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

            $responseData = json_decode($response->getBody()->getContents(), true);
            $rawContent = $responseData['choices'][0]['message']['content'] ?? '';

            $parsed = json_decode($rawContent, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['corrigido']) && isset($parsed['feedback'])) {
                return [
                    'corrigido' => $parsed['corrigido'],
                    'feedback' => $parsed['feedback'],

                    'cohesion' => $parsed['cohesion'] ?? '',
                    'coherence' => $parsed['coherence'] ?? '',
                    'grammar' => $parsed['grammar'] ?? '',
                    'vocabulary' => $parsed['vocabulary'] ?? '',
                    
                    'usage' => $responseData['usage'] ?? '',
                    'model' => $responseData['model'] ?? ''
                ];
            } else {
                return [
                    'error' => 'Erro ao interpretar resposta da IA como JSON.',
                    'raw' => $rawContent
                ];
            }

        } catch (\Exception $e) {
            return [
                'error' => 'Erro na requisição: ' . $e->getMessage()
            ];
        }

    }

    public function newIdea(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $prompt = <<<EOT
        You are an expert in English writing practice. Your task is to create a clear, interesting, and thought-provoking essay topic suitable for English learners. The topic should encourage the student to express their opinion or describe an experience, and it should match a general CEFR level (A2 to B2).
        Give the topic in this format JSON:
        {
            "topic": "<title>",
            "explanation": "<A short explanation of the theme>",
            "suggested_vocabulary": "<Suggested vocabulary with 5 to 10 useful words>"
        }
        EOT;

        $userMessage = "Only return one theme at a time.";

        $client = new Client();

        $data = [
            'model' => $model,
            'temperature' => $temperature,
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $userMessage]
            ]
        ];

        try {
            $response = $client->post($apiUrl, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            $rawContent = $responseData['choices'][0]['message']['content'] ?? '';

            $parsed = json_decode($rawContent, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['topic']) && isset($parsed['explanation'])) {
                return [
                    'topic' => $parsed['topic'],
                    'explanation' => $parsed['explanation'],
                    'suggested_vocabulary' => $parsed['suggested_vocabulary'],
                    'usage' => $responseData['usage'] ?? '',
                    'model' => $responseData['model'] ?? ''
                ];
            } else {
                return [
                    'error' => 'Erro ao interpretar resposta da IA como JSON.',
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
