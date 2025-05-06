<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Gramática',
            'Vocabulário',
            'Expressões Idiomáticas',
            'Tempos Verbais',
            'Pronomes',
            'Preposições',
            'Artigos',
            'Adjetivos',
            'Advérbios',
            'Falsos Cognatos',
            'Frases Comuns do Dia a Dia',
            'Phrasal Verbs',
            'Conjunções',
            'Perguntas e Respostas Frequentes',
            'Vocabulário por Tema',
            'Erros Comuns',
            'Tradução Inversa',
            'Verbos Irregulares'
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }
    }
}
