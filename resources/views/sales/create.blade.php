@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Nouvelle vente</h1>
                <p class="text-gray-600 mt-1">Enregistrer une nouvelle vente avec vérification automatique du stock</p>
            </div>
            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                ← Retour aux ventes
            </a>
        </div>
    </div>

    <!-- Formulaire de vente -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sélection du produit -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        📦 Produit *
                    </label>
                    <select name="product_id" id="product_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner un produit...</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-stock="{{ $product->stock->quantite ?? 0 }}"
                                data-seuil="{{ $product->stock->seuil ?? 0 }}"
                                data-prix="{{ $product->prix_vente }}"
                                data-nom="{{ $product->nom }}"
                                data-sku="{{ $product->sku }}">
                            {{ $product->sku }} - {{ $product->nom }} 
                            (Stock: {{ $product->stock->quantite ?? 0 }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantité avec boutons +/- -->
                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700 mb-2">
                        🔢 Quantité *
                    </label>
                    <div class="flex items-center space-x-2">
                        <!-- Bouton moins -->
                        <button type="button" id="decreaseBtn" 
                                class="w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold text-lg flex items-center justify-center transition-colors duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                style="min-width: 40px; min-height: 40px; display: flex !important;">
                            −
                        </button>
                        
                        <!-- Input quantité -->
                        <input type="number" name="quantite" id="quantite" min="1" value="1" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center font-semibold text-lg"
                               placeholder="1">
                        
                        <!-- Bouton plus -->
                        <button type="button" id="increaseBtn" 
                                class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold text-lg flex items-center justify-center transition-colors duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                style="min-width: 40px; min-height: 40px; display: flex !important;">
                            +
                        </button>
                    </div>
                    @error('quantite')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Alerte stock en temps réel -->
                    <div id="stockAlert" class="hidden mt-2 p-3 rounded-lg"></div>
                </div>

                <!-- Prix de vente (non modifiable) -->
                <div>
                    <label for="prix_unitaire" class="block text-sm font-medium text-gray-700 mb-2">
                        💰 Prix de vente (Fcfa) *
                    </label>
                    <input type="number" name="prix_unitaire" id="prix_unitaire" step="0.01" min="0" required readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 cursor-not-allowed font-semibold"
                           placeholder="Prix automatique selon le produit">
                    <p class="text-xs text-gray-500 mt-1">💡 Prix automatiquement défini selon le produit sélectionné</p>
                    @error('prix_unitaire')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total calculé -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        💵 Total calculé
                    </label>
                    <div id="totalCalcule" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-lg font-semibold text-green-600">
                        0 Fcfa
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Notes (optionnel)
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Notes sur la vente..."></textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informations produit sélectionné -->
            <div id="productInfo" class="hidden mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="font-semibold text-blue-900 mb-2">📋 Informations produit</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-blue-600 font-medium">SKU:</span>
                        <span id="infoSku" class="ml-1"></span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Stock:</span>
                        <span id="infoStock" class="ml-1"></span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Seuil:</span>
                        <span id="infoSeuil" class="ml-1"></span>
                    </div>
                    <div>
                        <span class="text-blue-600 font-medium">Prix suggéré:</span>
                        <span id="infoPrix" class="ml-1"></span>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('sales.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    💰 Enregistrer la vente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantiteInput = document.getElementById('quantite');
    const prixInput = document.getElementById('prix_unitaire');
    const totalDiv = document.getElementById('totalCalcule');
    const stockAlert = document.getElementById('stockAlert');
    const productInfo = document.getElementById('productInfo');
    const submitBtn = document.getElementById('submitBtn');
    
    // Boutons +/-
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    

    // Éléments d'information produit
    const infoSku = document.getElementById('infoSku');
    const infoStock = document.getElementById('infoStock');
    const infoSeuil = document.getElementById('infoSeuil');
    const infoPrix = document.getElementById('infoPrix');

    let currentStock = 0;
    let currentSeuil = 0;
    
    // Fonction pour mettre à jour l'état des boutons +/-
    function updateQuantityButtons() {
        const currentValue = parseInt(quantiteInput.value) || 0;
        
        // Vérifier que les boutons existent
        if (!decreaseBtn || !increaseBtn) {
            return;
        }
        
        // Bouton moins : désactivé si quantité <= 1
        decreaseBtn.disabled = currentValue <= 1;
        
        // Bouton plus : désactivé si on atteint le stock max
        increaseBtn.disabled = currentStock > 0 && currentValue >= currentStock;
    }
    
    // Initialisation des boutons au chargement
    updateQuantityButtons();

    // Quand on sélectionne un produit
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            currentStock = parseInt(selectedOption.dataset.stock) || 0;
            currentSeuil = parseInt(selectedOption.dataset.seuil) || 0;
            const prixSuggere = parseFloat(selectedOption.dataset.prix) || 0;
            
            // Remplir le prix suggéré
            prixInput.value = prixSuggere;
            
            // Afficher les informations produit
            infoSku.textContent = selectedOption.dataset.sku;
            infoStock.textContent = currentStock;
            infoSeuil.textContent = currentSeuil;
            infoPrix.textContent = prixSuggere + ' Fcfa';
            productInfo.classList.remove('hidden');
            
            // Vérifier la quantité si déjà saisie et mettre à jour les boutons
            updateQuantityButtons();
            checkStock();
            calculateTotal();
        } else {
            productInfo.classList.add('hidden');
            hideStockAlert();
            currentStock = 0;
            currentSeuil = 0;
            updateQuantityButtons();
        }
    });

    // Boutons +/- pour la quantité
    decreaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantiteInput.value) || 1;
        if (currentValue > 1) {
            quantiteInput.value = currentValue - 1;
            updateQuantityButtons();
            checkStock();
            calculateTotal();
        }
    });

    increaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantiteInput.value) || 0;
        const newValue = currentValue + 1;
        
        // Vérifier si on ne dépasse pas le stock
        if (newValue <= currentStock || currentStock === 0) {
            quantiteInput.value = newValue;
            updateQuantityButtons();
            checkStock();
            calculateTotal();
        }
    });

    // Vérification en temps réel de la quantité
    quantiteInput.addEventListener('input', function() {
        updateQuantityButtons();
        checkStock();
        calculateTotal();
    });

    function checkStock() {
        const quantite = parseInt(quantiteInput.value) || 0;
        
        if (quantite > 0 && currentStock > 0) {
            if (quantite > currentStock) {
                showStockAlert('error', `❌ Stock insuffisant ! Quantité demandée: ${quantite}, Stock disponible: ${currentStock}`);
                submitBtn.disabled = true;
            } else if ((currentStock - quantite) <= currentSeuil && (currentStock - quantite) > 0) {
                showStockAlert('warning', `⚠️ Attention ! Après cette vente, le stock sera faible (${currentStock - quantite} restants, seuil: ${currentSeuil})`);
                submitBtn.disabled = false;
            } else if ((currentStock - quantite) === 0) {
                showStockAlert('warning', `⚠️ Attention ! Cette vente épuisera complètement le stock de ce produit.`);
                submitBtn.disabled = false;
            } else {
                hideStockAlert();
                submitBtn.disabled = false;
            }
        } else {
            hideStockAlert();
            submitBtn.disabled = false;
        }
    }

    function showStockAlert(type, message) {
        stockAlert.className = `mt-2 p-3 rounded-lg ${type === 'error' ? 'bg-red-100 border border-red-300 text-red-800' : 'bg-orange-100 border border-orange-300 text-orange-800'}`;
        stockAlert.textContent = message;
        stockAlert.classList.remove('hidden');
    }

    function hideStockAlert() {
        stockAlert.classList.add('hidden');
    }

    function calculateTotal() {
        const quantite = parseInt(quantiteInput.value) || 0;
        const prix = parseFloat(prixInput.value) || 0;
        const total = quantite * prix;
        
        totalDiv.textContent = total.toLocaleString('fr-FR') + ' Fcfa';
        totalDiv.className = total > 0 ? 
            'w-full px-3 py-2 bg-green-100 border border-green-300 rounded-lg text-lg font-semibold text-green-600' :
            'w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-lg font-semibold text-gray-600';
    }

    // Validation avant soumission
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const quantite = parseInt(quantiteInput.value) || 0;
        
        if (quantite > currentStock) {
            e.preventDefault();
            alert('❌ Impossible de valider la vente : stock insuffisant !');
            return false;
        }
        
        if (!confirm(`Confirmer la vente de ${quantite} unité(s) pour un total de ${(quantite * (parseFloat(prixInput.value) || 0)).toLocaleString('fr-FR')} Fcfa ?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
