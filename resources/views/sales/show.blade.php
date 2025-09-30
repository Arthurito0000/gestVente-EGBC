@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📋 Détails de la vente</h1>
                <p class="text-gray-600 mt-1">Facture {{ $sale->numero_facture }}</p>
            </div>
            <div class="flex space-x-2">
                @can('edit-sales')
                <a href="{{ route('sales.edit', $sale) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    ✏️ Modifier
                </a>
                @endcan
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Détails de la vente -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informations générales -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Informations générales</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Numéro de facture:</span>
                        <span class="font-medium">{{ $sale->numero_facture }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date de vente:</span>
                        <span class="font-medium">{{ $sale->date_vente ? \Carbon\Carbon::parse($sale->date_vente)->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Vendeur:</span>
                        <span class="font-medium">{{ $sale->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Créée le:</span>
                        <span class="font-medium">{{ $sale->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Informations produit -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Produit vendu</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">SKU:</span>
                        <span class="font-medium">{{ $sale->product->sku ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nom:</span>
                        <span class="font-medium">{{ $sale->product->nom ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Catégorie:</span>
                        <span class="font-medium">{{ ucfirst($sale->product->categorie ?? 'N/A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Stock actuel:</span>
                        <span class="font-medium">
                            @if($sale->product && $sale->product->stock)
                                {{ $sale->product->stock->quantite }}
                                @if($sale->product->stock->quantite <= $sale->product->stock->seuil)
                                    <span class="text-orange-600">⚠️</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails financiers -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl shadow-md p-6 border border-green-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Détails financiers</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $sale->quantite }}</div>
                <div class="text-sm text-gray-600">Quantité</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($sale->prix_unitaire, 0, ',', ' ') }} Fcfa</div>
                <div class="text-sm text-gray-600">Prix unitaire</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($sale->total, 0, ',', ' ') }} Fcfa</div>
                <div class="text-sm text-gray-600">Total</div>
            </div>
            <div class="text-center">
                @if($sale->product && $sale->product->prix_achat)
                    @php
                        $benefice = $sale->total - ($sale->product->prix_achat * $sale->quantite);
                        $marge = $sale->total > 0 ? ($benefice / $sale->total) * 100 : 0;
                    @endphp
                    <div class="text-2xl font-bold {{ $benefice >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($benefice, 0, ',', ' ') }} Fcfa
                    </div>
                    <div class="text-sm text-gray-600">
                        Bénéfice ({{ number_format($marge, 1) }}%)
                    </div>
                @else
                    <div class="text-2xl font-bold text-gray-400">N/A</div>
                    <div class="text-sm text-gray-600">Bénéfice</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($sale->notes)
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-700">{{ $sale->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Actions</h3>
        <div class="flex flex-wrap gap-4">
            @can('edit-sales')
            <a href="{{ route('sales.edit', $sale) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                ✏️ Modifier cette vente
            </a>
            @endcan
            
            @can('create-sales')
            <a href="{{ route('sales.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                ➕ Nouvelle vente
            </a>
            @endcan
            
            @can('view-products')
            @if($sale->product)
            <a href="{{ route('products.show', $sale->product) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                📦 Voir le produit
            </a>
            @endif
            @endcan
            
            @can('delete-sales')
            <form method="POST" action="{{ route('sales.destroy', $sale) }}" 
                  style="display: inline;"
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Le stock sera restauré.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    🗑️ Supprimer
                </button>
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection
