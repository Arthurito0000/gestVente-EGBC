@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div>
    <h1 class="font-heading text-2xl text-gray-900">Modifier le produit</h1>
    <p class="text-gray-500">Modifiez les informations du produit {{ $product->sku }}.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            📦 SKU/Référence *
            <span class="block text-xs text-gray-400 italic">Ex: CJB pour COUVRE JOINT BLANC</span>
          </label>
          <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Code unique du produit" required />
          @error('sku')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Nom du produit *</label>
          <input type="text" name="nom" value="{{ old('nom', $product->nom ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Nom du produit" required />
          @error('nom')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">💰 Prix d'achat *</label>
          <div class="relative">
            <input type="number" name="prix_achat" value="{{ old('prix_achat', $product->prix_achat ?? '') }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" placeholder="0.00" required />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">Fcfa</span>
          </div>
          @error('prix_achat')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">💵 Prix de vente *</label>
          <div class="relative">
            <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente ?? '') }}" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" placeholder="0.00" required />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">Fcfa</span>
          </div>
          @error('prix_vente')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

       <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Catégorie <span class="text-gray-400">(optionnel)</span></label>
        <div class="relative">
          <select name="categorie" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-3 py-3 appearance-none">
            <option value="">Sélectionner...</option>
            @foreach($categories as $category)
              <option value="{{ $category->name }}" {{ old('categorie', $product->categorie ?? '') == $category->name ? 'selected' : '' }}>
                {{ $category->name }}
              </option>
            @endforeach
          </select>
          <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
            ▼
          </span>
        </div>
        @error('categorie')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

        <!-- <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
          <div class="relative">
            <input type="text" name="quantite" value="{{ old('quantite', $product->quantite ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-3 py-3" placeholder="0" />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">pcs</span>
          </div>
          @error('quantite')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div> -->

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">⚠️ Seuil d'alerte</label>
          <div class="relative">
            <input type="number" name="seuil_stock" value="{{ old('seuil_stock', $product->stock ? $product->stock->seuil : '10') }}" min="0" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" placeholder="10" />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">unités</span>
          </div>
          <p class="mt-1 text-xs text-gray-500">Alerte quand le stock descend sous ce seuil</p>
          @error('seuil_stock')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
        <a href="{{ route('products.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
          Annuler
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          ✏️ Modifier le produit
        </button>
      </div>    
    </form>
  </div>
</div>
@endsection
