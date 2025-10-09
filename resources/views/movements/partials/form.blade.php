@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $page }}</h1>
            <p class="text-gray-600 mt-1">Enregistrez un mouvement de stock (entrée, sortie ou ajustement)</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <p class="font-semibold">Erreur de validation :</p>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('movements.store') }}" method="POST" id="movementForm" class="bg-white rounded-lg shadow-md p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Produit avec recherche -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Produit *
                        <span class="text-xs font-normal text-gray-500 ml-2">(Recherchez par nom ou SKU)</span>
                    </label>
                    <select name="product_id" 
                            id="productSelect"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" 
                            required>
                        <option value="">Sélectionner un produit</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->nom }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <div class="flex gap-3 flex-wrap">
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors">
                            <input name="type" type="radio" value="ENTREE" class="text-green-600 focus:ring-green-600" {{ old('type', 'ENTREE') == 'ENTREE' ? 'checked' : '' }} onchange="toggleFields()">
                            <span class="text-green-700">ENTRÉE</span>
                        </label>
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="type" value="SORTIE" class="text-red-600 focus:ring-red-600" {{ old('type') == 'SORTIE' ? 'checked' : '' }} onchange="toggleFields()">
                            <span class="text-red-700">SORTIE</span>
                        </label>
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="type" value="AJUSTEMENT" class="text-blue-600 focus:ring-blue-600" {{ old('type') == 'AJUSTEMENT' ? 'checked' : '' }} onchange="toggleFields()">
                            <span class="text-blue-700">AJUSTEMENT</span>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type d'ajustement -->
                <div id="ajustement-type" class="{{ old('type') == 'AJUSTEMENT' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type d'ajustement *</label>
                    <div class="flex gap-3">
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors">
                            <input name="ajustement_type" type="radio" value="AUGMENTATIF" class="text-green-600 focus:ring-green-600" {{ old('ajustement_type') == 'AUGMENTATIF' ? 'checked' : '' }}>
                            <span class="text-green-700">Augmentatif (+)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors">
                            <input name="ajustement_type" type="radio" value="DIMINUTIF" class="text-red-600 focus:ring-red-600" {{ old('ajustement_type') == 'DIMINUTIF' ? 'checked' : '' }}>
                            <span class="text-red-700">Diminutif (-)</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Précisez si l'ajustement augmente ou diminue le stock</p>
                    @error('ajustement_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
           
                <!-- Quantité améliorée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantité *
                        <span class="text-xs font-normal text-gray-500 ml-2">
                            (accepte: nombres entiers, décimaux ou fractions)
                        </span>
                    </label>
                    
                    <!-- Exemples de formats -->
                    <div class="mb-2 flex flex-wrap gap-2">
                        <button type="button" onclick="setQuantity('10')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                            Ex: 10
                        </button>
                        <button type="button" onclick="setQuantity('2.5')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                            Ex: 2.5
                        </button>
                        <button type="button" onclick="setQuantity('3/4')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                            Ex: 3/4
                        </button>
                        <button type="button" onclick="setQuantity('1/2')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                            Ex: 1/2
                        </button>
                        <button type="button" onclick="setQuantity('0.25')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                            Ex: 0.25
                        </button>
                    </div>
                    
                    <div class="relative">
                        <!-- Champ visible pour l'utilisateur (saisie libre : fraction ou décimal) -->
                        <input 
                            id="quantiteInput"
                            type="text" 
                            value="{{ old('quantite') }}"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3 pr-20" 
                            placeholder="Ex: 10 ou 2.5 ou 3/4"
                            oninput="validateQuantity(this)"
                        />
                        <!-- Champ caché envoyé au serveur (contient toujours la valeur décimale) -->
                        <input type="hidden" name="quantite" id="quantiteDecimal" value="{{ old('quantite') }}" required>
                        
                        <div id="quantitePreview" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-xs"></span>
                        </div>
                    </div>
                    
                    <p class="mt-1 text-xs text-gray-500">
                        💡 Formats acceptés :
                        <span class="font-medium">entier</span> (10),
                        <span class="font-medium">décimal</span> (2.5),
                        <span class="font-medium">fraction</span> (3/4)
                    </p>
                    
                    <div id="quantiteError" class="mt-1 text-xs text-red-600 hidden"></div>
                    @error('quantite')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix d'achat -->
                <div id="prix-achat-field" class="{{ old('type') == 'AJUSTEMENT' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat unitaire (optionnel)</label>
                    <div class="relative">
                        <input name="prix_achat" type="number" step="0.01" min="0" value="{{ old('prix_achat') }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3 pr-12" placeholder="0.00"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">Fcfa</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Si renseigné, mettra à jour le prix d'achat du produit</p>
                    @error('prix_achat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input name="date" value="{{ old('date', Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" type="datetime-local" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" required/>
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Motif -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif *</label>
                
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
                <textarea id="motifTextarea" name="motif" rows="3" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:outline-none focus:ring-0 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Sélectionnez un motif ci-dessus ou saisissez un motif personnalisé..." required>{{ old('motif') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">💡 Cliquez sur un motif prédéfini ou saisissez votre propre motif</p>
                @error('motif')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('movements.index') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50 transition-colors">Annuler</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5 transition-colors">Enregistrer le mouvement</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
// 🔴 Fonction pour convertir une fraction en décimal (JavaScript)
function parseFraction(input) {
    const value = input.trim();
    
    if (!value) return 0;
    
    // Si c'est un nombre décimal normal
    if (!value.includes('/')) {
        return parseFloat(value) || 0;
    }
    
    // Si c'est une fraction (ex: 1/2, 3/4)
    const parts = value.split('/');
    if (parts.length === 2) {
        const numerator = parseFloat(parts[0]);
        const denominator = parseFloat(parts[1]);
        
        if (!isNaN(numerator) && !isNaN(denominator) && denominator !== 0) {
            return numerator / denominator;
        }
    }
    
    return 0;
}

// Initialiser Choices.js pour le select produit
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('productSelect');
    
    if (productSelect) {
        new Choices(productSelect, {
            searchEnabled: true,
            searchPlaceholderValue: 'Rechercher un produit (nom ou SKU)...',
            noResultsText: 'Aucun produit trouvé',
            itemSelectText: 'Cliquer pour sélectionner',
            shouldSort: false,
            removeItemButton: false,
            searchFields: ['label', 'value'],
            fuseOptions: {
                threshold: 0.3,
                distance: 100
            }
        });
    }
    
    toggleFields();
});

// Fonction pour basculer l'affichage des champs selon le type
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
        document.querySelectorAll('input[name="ajustement_type"]').forEach(input => {
            input.required = true;
        });
    } else {
        ajustementTypeDiv.classList.add('hidden');
        prixAchatField.classList.remove('hidden');
        document.querySelectorAll('input[name="ajustement_type"]').forEach(input => {
            input.required = false;
            input.checked = false;
        });
    }
}

// Fonction pour sélectionner un motif prédéfini
function selectPredefinedMotif(motif) {
    const textarea = document.getElementById('motifTextarea');
    textarea.value = motif;
    textarea.focus();
    
    textarea.style.backgroundColor = '#dbeafe';
    setTimeout(() => {
        textarea.style.backgroundColor = '';
    }, 500);
}

// Fonction pour définir une quantité exemple
function setQuantity(value) {
    const input = document.getElementById('quantiteInput');
    input.value = value;
    input.focus();
    validateQuantity(input);
    
    input.style.backgroundColor = '#dbeafe';
    setTimeout(() => {
        input.style.backgroundColor = '';
    }, 500);
}

// 🔴 Fonction pour valider la quantité et convertir en temps réel
function validateQuantity(input) {
    const value = input.value.trim();
    const preview = document.getElementById('quantitePreview').querySelector('span');
    const errorDiv = document.getElementById('quantiteError');
    const hiddenInput = document.getElementById('quantiteDecimal');
    
    // Réinitialiser
    errorDiv.classList.add('hidden');
    input.classList.remove('border-red-500');
    preview.textContent = '';
    
    if (!value) {
        hiddenInput.value = '';
        return;
    }
    
    // Vérifier le format
    const fractionPattern = /^(\d+\.?\d*)(\/\d+\.?\d*)?$/;
    
    if (!fractionPattern.test(value)) {
        errorDiv.textContent = '❌ Format invalide. Utilisez: 10, 2.5 ou 3/4';
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
        hiddenInput.value = '';
        return;
    }
    
    // Convertir et stocker la valeur décimale
    const decimal = parseFraction(value);
    
    if (decimal <= 0) {
        errorDiv.textContent = '❌ La quantité doit être supérieure à 0';
        errorDiv.classList.remove('hidden');
        input.classList.add('border-red-500');
        hiddenInput.value = '';
        return;
    }
    
    // Stocker la valeur décimale dans le champ caché
    hiddenInput.value = decimal;
    
    // Afficher l'aperçu pour les fractions
    if (value.includes('/')) {
        preview.textContent = `≈ ${decimal.toFixed(3)}`;
        preview.classList.remove('text-gray-400');
        preview.classList.add('text-green-600');
    } else {
        preview.textContent = '✓';
        preview.classList.remove('text-gray-400');
        preview.classList.add('text-green-600');
    }
}

// 🔴 Validation avant soumission du formulaire
document.getElementById('movementForm').addEventListener('submit', function(e) {
    const quantiteInput = document.getElementById('quantiteInput');
    const quantiteDecimal = document.getElementById('quantiteDecimal');
    
    // Vérifier si la conversion a bien été faite
    if (!quantiteDecimal.value || parseFloat(quantiteDecimal.value) <= 0) {
        e.preventDefault();
        
        const errorDiv = document.getElementById('quantiteError');
        errorDiv.textContent = '❌ Veuillez entrer une quantité valide';
        errorDiv.classList.remove('hidden');
        quantiteInput.classList.add('border-red-500');
        quantiteInput.focus();
        
        return false;
    }
});
</script>
@endpush
@endsection