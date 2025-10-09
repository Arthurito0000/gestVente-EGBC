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


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);
            
            $data = $validated;
            $data['code'] = 'CAT-' . Category::count() + 1;
            $category = Category::create($data);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catégorie ajoutée avec succès.',
                    'data' => $category
                ]);
            }
            
            return redirect()->route('categories.index')
                ->with('success', 'Catégorie ajoutée avec succès.');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création de la catégorie: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                ->with('error', 'Erreur lors de la création de la catégorie: ' . $e->getMessage());
        }
    }

    public function show( Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function destroy( $id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }

    public function update($id, Request $request)
    {
        try {
            $category = Category::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ]);
            
            $category->update($validated);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catégorie mise à jour avec succès.',
                    'data' => $category
                ]);
            }
            
            return redirect()->route('categories.index')
                ->with('success', 'Catégorie mise à jour avec succès.');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage(),
                    'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : []
                ], 422);
            }
            
            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la catégorie: ' . $e->getMessage());
        }
    }
}
