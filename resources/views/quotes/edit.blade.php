@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Modifier le Devis {{ $quote->numero_devis }}</h1>
            <p class="text-gray-600 mt-1">Modifiez les informations du devis</p>
        </div>

        <form action="{{ route('quotes.update', $quote) }}" method="POST" id="quoteForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informations client -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations Client</h2>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Client *</label>
                                <input type="text" 
                                       name="client_nom" 
                                       value="{{ old('client_nom', $quote->client_nom) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                       required>
                                @error('client_nom')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date du Devis *</label>
                                <input type="date" 
                                       name="date_devis" 
                                       value="{{ old('date_devis', $quote->date_devis->format('Y-m-d')) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                       required>
                                @error('date_devis')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Objet du Devis</label>
                                <textarea name="objet" 
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                                          placeholder="Ex: Fabrication et pose de 14 fenêtres en aluminium...">{{ old('objet', $quote->objet) }}</textarea>
                                @error('objet')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Articles du devis -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Articles du Devis</h2>

                        <div id="itemsContainer" class="space-y-4">
                            <!-- Les items seront ajoutés ici dynamiquement -->
                        </div>
                        
                        <!-- 🔴 BUG FIX B : Bouton unique pour ajouter un article -->
                        <div class="mt-4">
                            <button type="button" 
                                    onclick="addItem()" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                + Ajouter un article
                            </button>
                        </div>
                    </div>

                    <!-- Main d'oeuvre -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Main d'Œuvre</h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant Main d'Œuvre (Fcfa)</label>
                            <input type="number" 
                                   name="main_oeuvre" 
                                   id="mainOeuvreInput"
                                   min="0" 
                                   step="0.01"
                                   value="{{ old('main_oeuvre', $quote->main_oeuvre) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                   oninput="calculateTotals()">
                            @error('main_oeuvre')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Résumé</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center pb-2 border-b">
                                <span class="text-gray-600">Total Matériel:</span>
                                <span id="totalMateriel" class="font-semibold text-blue-600">0 Fcfa</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b">
                                <span class="text-gray-600">Total Main d'Œuvre:</span>
                                <span id="totalMainOeuvre" class="font-semibold text-orange-600">0 Fcfa</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t-2 border-gray-300">
                                <span class="text-lg font-bold text-gray-800">TOTAL:</span>
                                <span id="totalGeneral" class="text-xl font-bold text-green-600">0 Fcfa</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200">
                                Enregistrer les Modifications
                            </button>
                            <a href="{{ route('quotes.show', $quote) }}" 
                               class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 rounded-lg transition duration-200">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 0;
    const products = @json($products);
    const existingItems = @json($quote->items);

    function addItem(existingItem = null) {
        const container = document.getElementById('itemsContainer');
        const productId = existingItem ? existingItem.product_id : '';
        const designation = existingItem ? existingItem.designation : '';
        const quantite = existingItem ? existingItem.quantite : 1;
        const prixUnitaire = existingItem ? existingItem.prix_unitaire : '';
        
        const itemHtml = `
            <div class="item-row border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="${itemIndex}">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-semibold text-gray-700">Article #${itemIndex + 1}</h3>
                    <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produit * <span class="text-xs text-gray-500">(Recherchez par nom ou référence)</span></label>
                        <select name="items[${itemIndex}][product_id]" 
                                onchange="updateDesignation(${itemIndex})"
                                data-searchable
                                data-placeholder="Rechercher un produit (nom ou SKU)..."
                                class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                                required>
                            <option value="">Sélectionner un produit</option>
                            ${products.map(p => `<option value="${p.id}" ${p.id == productId ? 'selected' : ''} data-name="${p.nom}" data-sku="${p.sku}" data-price="${p.prix_vente}">${p.nom} (${p.sku})</option>`).join('')}
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Désignation *</label>
                        <input type="text" 
                               name="items[${itemIndex}][designation]" 
                               value="${designation}"
                               class="designation-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité *</label>
                        <input type="number" 
                               name="items[${itemIndex}][quantite]" 
                               min="1" 
                               value="${quantite}"
                               class="quantite-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                               oninput="calculateItemTotal(${itemIndex})"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix Unitaire (Fcfa) *</label>
                        <input type="number" 
                               name="items[${itemIndex}][prix_unitaire]" 
                               min="0" 
                               step="0.01"
                               value="${prixUnitaire}"
                               class="prix-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                               oninput="calculateItemTotal(${itemIndex})"
                               required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix Total</label>
                        <input type="text" 
                               class="total-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', itemHtml);
        
        // Initialiser le SearchableSelect pour le nouveau select
        const newSelect = container.querySelector(`[data-index="${itemIndex}"] select[data-searchable]`);
        if (newSelect && window.SearchableSelect) {
            new SearchableSelect(newSelect, {
                placeholder: 'Rechercher un produit (nom ou SKU)...',
                noResultsText: 'Aucun produit trouvé',
                onChange: (option) => {
                    updateDesignation(itemIndex);
                }
            });
        }
        
        if (existingItem) {
            calculateItemTotal(itemIndex);
        }
        
        itemIndex++;
    }

    function removeItem(index) {
        const item = document.querySelector(`[data-index="${index}"]`);
        if (item) {
            item.remove();
            calculateTotals();
        }
    }

    function updateDesignation(index) {
        const row = document.querySelector(`[data-index="${index}"]`);
        const select = row.querySelector('.product-select');
        const designationInput = row.querySelector('.designation-input');
        const prixInput = row.querySelector('.prix-input');
        
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value) {
            designationInput.value = selectedOption.dataset.name;
            prixInput.value = selectedOption.dataset.price;
            calculateItemTotal(index);
        }
    }

    function calculateItemTotal(index) {
        const row = document.querySelector(`[data-index="${index}"]`);
        const quantite = parseFloat(row.querySelector('.quantite-input').value) || 0;
        const prix = parseFloat(row.querySelector('.prix-input').value) || 0;
        const total = quantite * prix;
        
        row.querySelector('.total-input').value = total.toLocaleString('fr-FR') + ' Fcfa';
        calculateTotals();
    }

    function calculateTotals() {
        let totalMateriel = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantite = parseFloat(row.querySelector('.quantite-input').value) || 0;
            const prix = parseFloat(row.querySelector('.prix-input').value) || 0;
            totalMateriel += (quantite * prix);
        });

        const mainOeuvre = parseFloat(document.getElementById('mainOeuvreInput').value) || 0;
        const totalGeneral = totalMateriel + mainOeuvre;

        document.getElementById('totalMateriel').textContent = totalMateriel.toLocaleString('fr-FR') + ' Fcfa';
        document.getElementById('totalMainOeuvre').textContent = mainOeuvre.toLocaleString('fr-FR') + ' Fcfa';
        document.getElementById('totalGeneral').textContent = totalGeneral.toLocaleString('fr-FR') + ' Fcfa';
    }

    // Charger les items existants
    document.addEventListener('DOMContentLoaded', function() {
        existingItems.forEach(item => {
            addItem(item);
        });
        
        if (existingItems.length === 0) {
            addItem();
        }
    });
</script>
@endpush
@endsection
