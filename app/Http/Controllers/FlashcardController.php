<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Category;
use App\Models\Flashcards;
use App\Models\FlashcardItems;
use Carbon\Carbon;
use Mpdf\Mpdf;
use GuzzleHttp\Client;

class FlashcardController extends Controller
{
    private $categoryModel;
    private $flashcardModel;
    private $flashcardItemModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->flashcardModel = new Flashcards();
        $this->flashcardItemModel = new FlashcardItems();
    }

    public function index()
    {
        $data = $this->flashcardModel
                    ->withCount('flashcardItems')
                    ->orderBy('created_at', 'desc')
                    ->paginate();
        
        return view('flashcard.index', ['data' => $data]);
    }

    public function create()
    {
        $categories = $this->categoryModel->get();

        return view('flashcard.create', ['categories' => $categories]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required'],
        ],
        [
            'required'  => 'Campo obrigatório',
        ]);

        try {
            $this->flashcardModel->title = $request->title;
            $this->flashcardModel->category_id = $request->category_id;
            $this->flashcardModel->created_at = now();
            $this->flashcardModel->save();

            $insertedId = $this->flashcardModel->id;
        } catch (\Exception $e) {
            return redirect()->route('flashcard')->with('error', 'Ocorreu um erro. Verifique os campos.');
        }

        return redirect()->route('flashcard.show', $insertedId)->with('success', 'Flashcard criado, comece a praticar.');
    }

    public function show(Request $request)
    {
        $flashcard = $this->flashcardModel->where('id', $request->id)->first();
     
        if (!$flashcard) {
            return redirect()->route('flashcard')->with('error', 'Flashcard não encontrado.');

        }

        return view('flashcard.show', ['flashcard' => $flashcard]);
    }

    public function getStats()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $items = $this->flashcardItemModel->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                                        ->where('deleted', 0)
                                        ->get();

        $frequency = [];
        $levelsPerDay = [];

        foreach (range(0, 6) as $i) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $frequency[$date] = 0;
            $levelsPerDay[$date] = [];
        }

        foreach ($items as $item) {
            $day = Carbon::parse($item->created_at)->format('Y-m-d');
            $frequency[$day] = ($frequency[$day] ?? 0) + 1;
            $levelsPerDay[$day][] = $item->level;
        }

        return response()->json([
            'frequency' => $frequency,
            'levelsPerDay' => $levelsPerDay
        ]);
    }







    public function generatePdf($id)
    {
        $flashcard = $this->flashcardModel->find($id);
        $practices = $this->flashcardItemModel->where('flashcard_id', $id)->get();

        $html = view('pdf.all', compact('practices', 'flashcard'))->render();

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->SetFooter('Página {PAGENO} de {nbpg}');

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="pratica.pdf"',
        ]);
    }









    public function newIdea(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $prompt = <<<EOT
        Você é um especialista em criar frases em inglês para ajudar os alunos a praticarem. Baseado no título do flashcard abaixo, crie uma pergunta ou frase para o aluno refletir e responder sobre o tema.".
        EOT;

        $userMessage = "O título do flashcard é: {$request->flashcard_title}";

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


    public function newWord(Request $request)
    {
        $apiKey = env('CHATGPT_KEY');
        $model = 'gpt-4o-mini';
        $temperature = 1;
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $prompt = <<<EOT
        Você é um especialista em criar palavras em inglês para ajudar os alunos a praticarem.
        Baseado nas informações abaixo, gere uma lista de palavras relevantes ao tema.
        Retorne as palavras separadas por vírgula e sem numeração.
        EOT;
        
        $userMessage = "O título do flashcard é: {$request->flashcard_title}, a frase atual é: {$request->idea_phrase} e o nível de inglês é: {$request->level}";
        
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