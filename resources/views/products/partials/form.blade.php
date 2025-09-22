<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
          <input type="text" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3" placeholder="SKU-000"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
          <input type="text" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3" placeholder="Nom du produit"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat</label>
          <div class="relative">
            <input type="number" step="0.01" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors pl-10 pr-3 py-3" placeholder="0.00"/>
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">€</span>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prix de vente</label>
          <div class="relative">
            <input type="number" step="0.01" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors pl-10 pr-3 py-3" placeholder="0.00"/>
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">€</span>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">Créer</button>
      </div>    