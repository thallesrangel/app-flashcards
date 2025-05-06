<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    public function index()
    {
        return Category::all();
    }

    public function store(Request $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function list()
    {
        $categories = Category::all();
        return view('components.category_radios', compact('categories'))->render();
    }
}
