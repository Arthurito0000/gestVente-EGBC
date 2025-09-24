<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Produit</label>
          <select name="product_id" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3">
            @foreach ($products as $product)
              <option value="{{ $product->id }}">{{ $product->sku }} — {{ $product->nom }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
          <div class="flex gap-3">
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer">
              <input name="type" type="radio" selected name="type" value="ENTREE" class="text-green-600 focus:ring-green-600" checked>
              <span>ENTREE</span>
            </label>
            <!-- <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer">
              <input type="radio" name="type" value="SORTIE" class="text-red-600 focus:ring-red-600">
              <span>SORTIE</span>
            </label> -->
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
          <input name="quantite" type="number" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="0" required/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat unitaire (optionnel)</label>
          <div class="relative">
            <input name="prix_achat" type="number" step="0.01" min="0" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3 pr-12" placeholder="0.00"/>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <span class="text-gray-500 text-sm">Fcfa</span>
            </div>
          </div>
          <p class="mt-1 text-xs text-gray-500">Si renseigné, mettra à jour le prix d'achat du produit</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
          <input name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" type="datetime-local" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3"/>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Motif</label>
        <textarea name="motif" rows="3" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Ex: Réception fournisseur, vente, ajustement..."></textarea>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('movements.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">Enregistrer</button>
      </div>