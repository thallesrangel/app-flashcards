<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\WeeklyMissions;

class WeeklyMissionsController extends Controller
{
    protected $weeklyMissionModel;

    public function __construct(WeeklyMissions $weeklyMissionModel)
    {
        $this->weeklyMissionModel = $weeklyMissionModel;
    }

    public function index()
    {
        $data = $this->weeklyMissionModel->get();

        return view('weekly-missions.index', ['data' => $data ]);
    }

    public function create()
    {
        return view('weekly-missions.create', ['data' => '']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required'],
            'past_due' => ['required'],
        ],
        [
            'required'  => 'Campo obrigatório',
        ]);
        
        try {
            $this->weeklyMissionModel->title = $request->title;
            $this->weeklyMissionModel->description = $request->description;
            $this->weeklyMissionModel->past_due = $request->past_due;
            $this->weeklyMissionModel->created_at = now();
            $this->weeklyMissionModel->save();

            $insertedId = $this->weeklyMissionModel->id;
        } catch (\Exception $e) {
            return redirect()->route('weekly-missions')->with('error', 'Ocorreu um erro. Verifique os campos.');
        }

        return redirect()->route('weekly-missions.show', $insertedId)->with('success', 'Created');
    }
    
    public function show(Request $request)
    {
        $mission = $this->weeklyMissionModel->where('id', $request->id)->first();
     
        if (!$mission) {
            return redirect()->route('flashcard')->with('error', 'mission not found.');
        }

        return view('weekly-missions.show', ['mission' => $mission]);
    }


    public function checkText(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $missionTitle = $request->title;
        $missionDescription = $request->description;
        $studentText = $request->content;

        $prompt = <<<EOT
        Você é um professor de inglês. Sua tarefa é corrigir um texto enviado por um aluno, mantendo a correção em **inglês**, e fornecer um **feedback separado em português**.

        Missão do aluno:
        Título: "{$missionTitle}"
        Descrição: "{$missionDescription}"

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

        $userMessage = "Texto do aluno:\n{$studentText}";

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
}
