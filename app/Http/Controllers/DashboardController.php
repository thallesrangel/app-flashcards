<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Flashcards;
use App\Models\FlashcardItems;

class DashboardController extends Controller
{
    private $flashcardModel;
    private $flashcardItemModel;

    public function __construct()
    {
        $this->flashcardModel = new Flashcards();
        $this->flashcardItemModel = new FlashcardItems();
    }

    public function index()
    {
        $flashcardsCount = $this->flashcardModel->count();
        $flashcardItemsCount = $this->flashcardItemModel->count();


        $practiceDays = $this->flashcardItemModel->where('deleted', 0)
                    ->selectRaw('DATE(created_at) as day')
                    ->groupBy('day')
                    ->orderByDesc('day')
                    ->pluck('day')
                    ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
                    ->toArray();


        $streak = 0;
        $current = \Carbon\Carbon::today();
    
        while (in_array($current->toDateString(), $practiceDays)) {
            $streak++;
            $current->subDay();
        }


        //
        $cardsPerDay = $this->cardCounts();

        //
        $weeklyPractice = $this->weeklyPractice();

        //
        $categoryStats = $this->usagePerCategory();

        //
        $timeCategoryStats = $this->timePerCategory();


        return view('dashboard.index', [
            'flashcardsCount' => $flashcardsCount,
            'flashcardItemsCount' => $flashcardItemsCount,
            'practiceStreak' => $streak,
            'cardsPerDay' => $cardsPerDay,
            'weeklyData' => $weeklyPractice['weeklyData'],
            'maxWeekly' => $weeklyPractice['maxWeekly'],
            'categoryStats' => $categoryStats,
            'timeCategoryStats' => $timeCategoryStats
        ]);
    }

    private function cardCounts() 
    {
        $startDate = \Carbon\Carbon::today()->subDays(29);
        $dates = collect();

        for ($i = 0; $i < 30; $i++) {
            $dates->put($startDate->copy()->addDays($i)->toDateString(), 0);
        }

        $cardCounts = $this->flashcardItemModel
            ->where('deleted', 0)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day');

        return $dates->merge($cardCounts);
    }

    private function weeklyPractice()
    {

        $weeklyData = $this->flashcardItemModel
            ->where('deleted', 0)
            ->selectRaw('YEARWEEK(created_at, 1) as year_week, MIN(DATE(created_at)) as week_start, COUNT(*) as total')
            ->groupBy('year_week')
            ->orderByDesc('week_start')
            ->take(6) // últimas 6 semanas
            ->get()
            ->map(function ($item) {
                return [
                    'week_start' => \Carbon\Carbon::parse($item->week_start)->format('d/m/Y'),
                    'total' => $item->total,
                ];
            });

        $maxWeekly = $weeklyData->max('total') ?: 1;

        return [
            'weeklyData' => $weeklyData,
            'maxWeekly' => $maxWeekly,
        ];
    }

    private function usagePerCategory()
    {
        $items = $this->flashcardItemModel->with('flashcard')->get();

        // Agrupar por categoria_id do flashcard pai
        $grouped = $items->groupBy(function ($item) {
            return $item->flashcard->category_id ?? null;
        })->filter();

        // Obter o total de práticas por categoria
        $categoryData = $grouped->map(function ($items, $categoryId) {
            return [
                'category_id' => $categoryId,
                'total' => $items->count(),
            ];
        })->sortByDesc('total')->values();

        // Total geral para porcentagem
        $totalPractices = $categoryData->sum('total');

        // Buscar os nomes das categorias
        $categories = \App\Models\Category::whereIn('id', $categoryData->pluck('category_id'))->pluck('name', 'id');

        // Montar estrutura final
        $categoryStats = $categoryData->map(function ($cat) use ($totalPractices, $categories) {
            return [
                'name' => $categories[$cat['category_id']] ?? 'Sem nome de categoria',
                'percent' => round(($cat['total'] / $totalPractices) * 100),
                'total' => $cat['total'],
            ];
        });

        return $categoryStats;
    }


    private function timePerCategory()
    {
        $items = $this->flashcardItemModel->with('flashcard.category')->get();

        // Agrupa por categoria
        $grouped = $items->groupBy(fn($item) => optional($item->flashcard->category)->name);

        // Conta total de práticas
        $total = $items->count();

        // Calcula percentuais
        $timeCategoryStats = $grouped->map(function ($items, $category) use ($total) {
            $count = $items->count();
            $percent = round(($count / $total) * 100);
            return [
                'name' => $category ?? 'Sem categoria',
                'total' => $count,
                'percent' => $percent,
            ];
        })->sortByDesc('percent');

        return $timeCategoryStats;
    }
}