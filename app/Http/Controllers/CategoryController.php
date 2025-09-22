<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCtegoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::oldest('id')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function store(StoreCtegoryRequest $request)
    {
        $data = $request->validated();
        $data['code'] = 'CAT-' . Category::count() + 1;
        $category = Category::create($data);
        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès.');
    }
}
