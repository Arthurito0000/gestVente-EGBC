@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">{{ $product->nom }}</h1>
      <p class="text-gray-500">Détails du produit {{ $product->sku }}</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2">Modifier</a>
      <a href="{{ route('products.index') }}" class="border rounded-lg px-4 py-2">Retour à la liste</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900 font-mono">
          {{ $product->sku }}
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
          {{ $product->nom }}
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
          {{ number_format($product->prix_achat, 2, ',', ' ') }} Fcfa
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
          @if($product->categorie)
            <span class="px-2 py-1 text-sm rounded-full bg-primary-100 text-primary-700">
              {{ ucfirst($product->categorie) }}
            </span>
          @else
            <span class="px-2 py-1 text-sm rounded-full bg-gray-100 text-gray-500">
              Non définie
            </span>
          @endif
        </div>
      </div>
      
      <!-- <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en stock</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
          {{ $product->quantite }} pcs
          @if($product->quantite <= $product->seuil_stock)
            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Stock faible</span>
          @endif
        </div>
      </div> -->
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de stock</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
          {{ $product->seuil_stock }} pcs
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Créé le</label>
        <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-gray-900">
          {{ $product->created_at->format('d/m/Y à H:i') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
