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
          <div class="flex gap-3 flex-wrap">
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50">
              <input name="type" type="radio" value="ENTREE" class="text-green-600 focus:ring-green-600" checked onchange="toggleFields()">
              <span class="text-green-700">ENTRÉE</span>
            </label>
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50">
              <input type="radio" name="type" value="SORTIE" class="text-red-600 focus:ring-red-600" onchange="toggleFields()">
              <span class="text-red-700">SORTIE</span>
            </label>
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50">
              <input type="radio" name="type" value="AJUSTEMENT" class="text-blue-600 focus:ring-blue-600" onchange="toggleFields()">
              <span class="text-blue-700">AJUSTEMENT</span>
            </label>
          </div>
        </div>

        <!-- Champ spécifique pour les ajustements -->
        <div id="ajustement-type" class="hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Type d'ajustement</label>
          <div class="flex gap-3">
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50">
              <input name="ajustement_type" type="radio" value="AUGMENTATIF" class="text-green-600 focus:ring-green-600">
              <span class="text-green-700">Augmentatif (+)</span>
            </label>
            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50">
              <input name="ajustement_type" type="radio" value="DIMINUTIF" class="text-red-600 focus:ring-red-600">
              <span class="text-red-700">Diminutif (-)</span>
            </label>
          </div>
          <p class="mt-1 text-xs text-gray-500">Précisez si l'ajustement augmente ou diminue le stock</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
          <input name="quantite" type="number" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="0" required/>
        </div>
        <div id="prix-achat-field">
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
        
        <!-- Motifs prédéfinis -->
        <div class="mb-3">
          <p class="text-xs text-gray-600 mb-2">Motifs fréquents :</p>
          <div class="flex flex-wrap gap-2">
            <button type="button" onclick="selectPredefinedMotif('Réapprovisionnement fournisseur')" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
              📦 Réapprovisionnement
            </button>
            <button type="button" onclick="selectPredefinedMotif('Retour client')" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">
              ↩️ Retour client
            </button>
            <button type="button" onclick="selectPredefinedMotif('Produit défectueux')" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">
              ❌ Produit défectueux
            </button>
            <button type="button" onclick="selectPredefinedMotif('Inventaire physique')" class="px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-full hover:bg-purple-200 transition-colors">
              📋 Inventaire
            </button>
            <button type="button" onclick="selectPredefinedMotif('Transfert entre magasins')" class="px-3 py-1 text-xs bg-orange-100 text-orange-700 rounded-full hover:bg-orange-200 transition-colors">
              🚚 Transfert
            </button>
            <button type="button" onclick="selectPredefinedMotif('Échantillon gratuit')" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors">
              🎁 Échantillon
            </button>
            <button type="button" onclick="selectPredefinedMotif('Perte/Vol')" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
              🚫 Perte/Vol
            </button>
          </div>
        </div>
        
        <!-- Champ de saisie libre -->
        <textarea id="motifTextarea" name="motif" rows="3" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Sélectionnez un motif ci-dessus ou saisissez un motif personnalisé..."></textarea>
        <p class="mt-1 text-xs text-gray-500">💡 Cliquez sur un motif prédéfini ou saisissez votre propre motif</p>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('movements.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">Enregistrer</button>
      </div>

<script>
function toggleFields() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const ajustementTypeDiv = document.getElementById('ajustement-type');
    const prixAchatField = document.getElementById('prix-achat-field');
    
    let selectedType = '';
    typeRadios.forEach(radio => {
        if (radio.checked) {
            selectedType = radio.value;
        }
    });
    
    if (selectedType === 'AJUSTEMENT') {
        ajustementTypeDiv.classList.remove('hidden');
        prixAchatField.classList.add('hidden');
        // Rendre le champ ajustement_type requis
        document.querySelectorAll('input[name="ajustement_type"]').forEach(input => {
            input.required = true;
        });
    } else {
        ajustementTypeDiv.classList.add('hidden');
        prixAchatField.classList.remove('hidden');
        // Retirer l'obligation du champ ajustement_type
        document.querySelectorAll('input[name="ajustement_type"]').forEach(input => {
            input.required = false;
            input.checked = false;
        });
    }
}

function selectPredefinedMotif(motif) {
    const textarea = document.getElementById('motifTextarea');
    textarea.value = motif;
    textarea.focus();
    
    // Animation visuelle pour indiquer la sélection
    textarea.style.backgroundColor = '#dbeafe';
    setTimeout(() => {
        textarea.style.backgroundColor = '';
    }, 500);
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>