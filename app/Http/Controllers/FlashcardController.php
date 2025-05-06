<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Category;
use App\Models\Flashcards;
use App\Models\FlashcardItems;
use Carbon\Carbon;
use Mpdf\Mpdf;

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
        $data = $this->flashcardModel->withCount('flashcardItems')->paginate();
        
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
}