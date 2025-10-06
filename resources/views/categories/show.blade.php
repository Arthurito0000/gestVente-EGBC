@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-heading text-2xl text-gray-900">{{ $category->name }}</h1>
                <p class="text-gray-500">Détails de la categorie </p>
            </div>
            <div class="flex items-center gap-3">

                <a href="{{ route('categories.index') }}" class="border rounded-lg px-4 py-2">Retour à la liste</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">category name:</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900 font-mono">
                        {{ $category->name }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category description:</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
                        {{ $category->description }}
                    </div>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Créé le</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
                        {{ $category->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
