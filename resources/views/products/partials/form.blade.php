<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">SKU/Reference (code unique ) <br> <span class="text-gray-400 italic">Exp:CJB pour COUVRE JOINT BLANC</span></label>
          <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="SKU" />
          @error('sku')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
          <input type="text" name="nom" value="{{ old('nom', $product->nom ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Nom du produit" />
          @error('nom')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat</label>
          <div class="relative">
            <input type="text" name="prix_achat" value="{{ old('prix_achat', $product->prix_achat ?? '') }}"  class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-3 py-3" placeholder="prix d'achat" />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">Fcfa</span>
          </div>
          @error('prix_achat')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prix de vente</label>
          <div class="relative">
            <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente ?? '') }}" step="0.01" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-10 pr-3 py-3" placeholder="0.00" required/>
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">Fcfa</span>
          </div>
          @error('prix_vente')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
       <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie <span class="text-gray-400">(optionnel)</span></label>
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

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
          <div class="relative">
            <input type="text" name="quantite" value="{{ old('quantite', $product->quantite ?? '') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-3 py-3" placeholder="0" />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">pcs</span>
          </div>
          @error('quantite')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Seuil de stock</label>
          <div class="relative">
            <input type="text" name="seuil_stock" value="{{ old('seuil_stock', $product->seuil_stock ?? '10') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors pl-3 pr-3 py-3" placeholder="10" />
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">pcs</span>
          </div>
          @error('seuil_stock')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">
          {{ isset($product) ? 'Modifier' : 'Créer' }}
        </button>
      </div>    