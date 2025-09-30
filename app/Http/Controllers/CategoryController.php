<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCtegoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    
    $categories = Category::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                       ->orWhere('description', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
        })
        ->oldest('id')
        ->paginate(10);
    
    // Conserver les paramètres de recherche dans la pagination
    $categories->appends($request->query());

    if ($request->ajax()) {
        return view('categories.partials.table', compact('categories'))->render();
    }

    return view('categories.index', [
        'categories' => $categories,
        'search' => $search,
        'page' => 'Gestions des catégories',
    ]);
}


    public function store(StoreCtegoryRequest $request)
    {
        $data = $request->validated();
        $data['code'] = 'CAT-' . Category::count() + 1;
        $category = Category::create($data);
        return redirect()->route('categories.index')->with('success', 'Catégorie ajoutée avec succès.');
    }
}
