<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlashcardItems;
use GuzzleHttp\Client;
use Mpdf\Mpdf;

class FlashcardItemController extends Controller
{
    private $flashcardModel;

    public function __construct()
    {
        $this->flashcardItemModel = new FlashcardItems();
    }
    
    public function checkText(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $prompt = <<<EOT
        Você é um professor de inglês. Sua tarefa é corrigir o texto enviado pelo aluno (mantendo a correção em inglês) e fornecer um feedback separado, claro e construtivo **em português**.
        Explique claramente, se necessário, alguma correção.    
        Retorne no seguinte formato JSON:
        {
        "corrigido": "<texto corrigido em inglês>",
        "feedback": "<comentário construtivo e breve em português sobre o texto apenas se necessário.>"
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

    public function storePractice(Request $request)
    {
        $validated = $request->validate([
            'original' => 'required|string',
            'corrigido' => 'required|string',
            'feedback' => 'required|string',
            'level' => 'required|string',
            'flashcard_id' => 'required|exists:flashcards,id'
        ]);

        $practice = new FlashcardItems();
        $practice->flashcard_id = $validated['flashcard_id'];
        $practice->content = $validated['original'];
        $practice->corrected_content = $validated['corrigido'];
        $practice->feedback = $validated['feedback'];
        $practice->level = $validated['level'];
        $practice->save();

        return response()->json([
            'message' => 'Prática salva com sucesso!',
            'item' => $practice
        ]);
    }

    public function listByFlashcard($flashcard_id)
    {
        $items = FlashcardItems::where('flashcard_id', $flashcard_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                $item->created_at_formatted = $item->created_at->format('d/m/Y H:i');
                return $item;
            });

        return response()->json(['items' => $items]);
    }

    public function generatePdf($id)
    {
        $practice = $this->flashcardItemModel->where('id', $id)->first();
    

        $html = view('pdf.partial', compact('practice'))->render();

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->SetFooter('Página {PAGENO} de {nbpg}');

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="pratica.pdf"',
        ]);
    }
}