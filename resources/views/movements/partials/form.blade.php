<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Produit</label>
          <select class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3">
            <option>SKU-001 — Souris Optique</option>
            <option>SKU-114 — Clavier Mécanique</option>
            <option>SKU-221 — Écran 24"</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
          <div class="flex gap-3">
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer">
              <input type="radio" name="type" value="ENTREE" class="text-green-600 focus:ring-green-600" checked>
              <span>ENTREE</span>
            </label>
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer">
              <input type="radio" name="type" value="SORTIE" class="text-red-600 focus:ring-red-600">
              <span>SORTIE</span>
            </label>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
          <input type="number" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3" placeholder="0"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
          <input type="datetime-local" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3"/>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Motif</label>
        <textarea rows="3" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-primary-600 focus:border-primary-600 transition-colors px-3 py-3" placeholder="Ex: Réception fournisseur, vente, ajustement..."></textarea>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('movements.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">Enregistrer</button>
      </div>