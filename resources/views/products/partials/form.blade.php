<!-- Informations de base du produit -->
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- SKU -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                📦 SKU/Référence *
                <span class="block text-xs text-gray-400 italic">Ex: CJB pour COUVRE JOINT BLANC</span>
            </label>
            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" 
                   class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-3" 
                   placeholder="Code unique du produit" required />
            @error('sku')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nom -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Nom du produit *</label>
            <input type="text" name="nom" value="{{ old('nom', $product->nom ?? '') }}" 
                   class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-3" 
                   placeholder="Nom du produit" required />
            @error('nom')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prix d'achat -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">💰 Prix d'achat *</label>
            <div class="relative">
                <input type="number" name="prix_achat" value="{{ old('prix_achat', $product->prix_achat ?? '') }}" 
                       step="0.01" min="0"
                       class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" 
                       placeholder="0.00" required />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">Fcfa</span>
            </div>
            @error('prix_achat')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prix de vente -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">💵 Prix de vente *</label>
            <div class="relative">
                <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente ?? '') }}" 
                       step="0.01" min="0"
                       class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" 
                       placeholder="0.00" required />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">Fcfa</span>
            </div>
            @error('prix_vente')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catégorie -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                🏷️ Catégorie <span class="text-gray-400">(optionnel)</span>
            </label>
            <div class="relative">
                <select name="categorie" 
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pl-3 pr-10 py-3 appearance-none">
                    <option value="">Sélectionner une catégorie...</option>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('categorie', $product->categorie ?? '') == $category->name ? 'selected' : '' }}>
                                {{ ucfirst($category->name) }}
                            </option>
                        @endforeach
                    @else
                        <option value="electronique">Électronique</option>
                        <option value="alimentaire">Alimentaire</option>
                        <option value="vetements">Vêtements</option>
                        <option value="autre">Autre</option>
                    @endif
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
            @error('categorie')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Stock initial -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">📦 Stock initial *</label>
            <div class="relative">
                <input type="number" name="quantite" value="{{ old('quantite', isset($product) && $product->stock ? $product->stock->quantite : '') }}" 
                       min="0"
                       class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" 
                       placeholder="0" required />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">unités</span>
            </div>
            @error('quantite')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Seuil d'alerte -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">⚠️ Seuil d'alerte *</label>
            <div class="relative">
                <input type="number" name="seuil_stock" value="{{ old('seuil_stock', isset($product) && $product->stock ? $product->stock->seuil : '10') }}" 
                       min="0"
                       class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors pl-3 pr-16 py-3" 
                       placeholder="10" required />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">unités</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">Alerte quand le stock descend sous ce seuil</p>
            @error('seuil_stock')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Calcul automatique de la marge -->
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <h4 class="text-sm font-medium text-blue-900 mb-2">📊 Calcul de marge automatique</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-blue-700">Marge unitaire:</span>
                <span id="margeUnitaire" class="ml-2 font-semibold text-blue-900">0 Fcfa</span>
            </div>
            <div>
                <span class="text-blue-700">Pourcentage:</span>
                <span id="margePourcentage" class="ml-2 font-semibold text-blue-900">0%</span>
            </div>
            <div>
                <span class="text-blue-700">Valeur stock:</span>
                <span id="valeurStock" class="ml-2 font-semibold text-blue-900">0 Fcfa</span>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
        <a href="{{ route('products.index') }}" 
           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            Annuler
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            {{ isset($product) ? '✏️ Modifier le produit' : '➕ Créer le produit' }}
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prixAchatInput = document.querySelector('input[name="prix_achat"]');
    const prixVenteInput = document.querySelector('input[name="prix_vente"]');
    const quantiteInput = document.querySelector('input[name="quantite"]');
    const margeUnitaireSpan = document.getElementById('margeUnitaire');
    const margePourcentageSpan = document.getElementById('margePourcentage');
    const valeurStockSpan = document.getElementById('valeurStock');

    function calculerMarge() {
        const prixAchat = parseFloat(prixAchatInput.value) || 0;
        const prixVente = parseFloat(prixVenteInput.value) || 0;
        const quantite = parseInt(quantiteInput.value) || 0;

        const margeUnitaire = prixVente - prixAchat;
        const margePourcentage = prixVente > 0 ? (margeUnitaire / prixVente) * 100 : 0;
        const valeurStock = prixAchat * quantite;

        margeUnitaireSpan.textContent = margeUnitaire.toLocaleString('fr-FR') + ' Fcfa';
        margePourcentageSpan.textContent = margePourcentage.toFixed(1) + '%';
        valeurStockSpan.textContent = valeurStock.toLocaleString('fr-FR') + ' Fcfa';

        // Couleurs selon la marge
        if (margePourcentage < 10) {
            margeUnitaireSpan.className = 'ml-2 font-semibold text-red-600';
            margePourcentageSpan.className = 'ml-2 font-semibold text-red-600';
        } else if (margePourcentage < 25) {
            margeUnitaireSpan.className = 'ml-2 font-semibold text-orange-600';
            margePourcentageSpan.className = 'ml-2 font-semibold text-orange-600';
        } else {
            margeUnitaireSpan.className = 'ml-2 font-semibold text-green-600';
            margePourcentageSpan.className = 'ml-2 font-semibold text-green-600';
        }
    }

    // Calcul en temps réel
    prixAchatInput.addEventListener('input', calculerMarge);
    prixVenteInput.addEventListener('input', calculerMarge);
    quantiteInput.addEventListener('input', calculerMarge);

    // Calcul initial
    calculerMarge();
});
</script>    