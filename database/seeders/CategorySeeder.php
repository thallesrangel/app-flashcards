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
            'Grammar',
            'Vocabulary',
            'Idiomatic Expressions',
            'Verb Tenses',
            'Pronouns',
            'Prepositions',
            'Articles',
            'Adjectives',
            'Adverbs',
            'False Cognates',
            'Common Everyday Phrases',
            'Phrasal Verbs',
            'Conjunctions',
            'Frequently Asked Questions',
            'Thematic Vocabulary',
            'Common Mistakes',
            'Reverse Translation',
            'Irregular Verbs'
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }
    }
}
