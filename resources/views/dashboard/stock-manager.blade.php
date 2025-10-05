<!-- Dashboard Gérant de Stock - Focus stock et produits -->

<!-- KPIs Stock -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- Total produits -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Total produits</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($total_produits ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Dans le catalogue</div>
            </div>
            <div class="text-3xl opacity-80">📦</div>
        </div>
    </div>

    <!-- Valeur stock -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Valeur du stock</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($valeur_stock ?? 0, 0, ',', ' ') }} Fcfa</div>
                <div class="text-xs mt-1 opacity-90">Inventaire total</div>
            </div>
            <div class="text-3xl opacity-80">💰</div>
        </div>
    </div>

    <!-- Entrées période -->
    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Entrées</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($entrees_periode ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Sur la période</div>
            </div>
            <div class="text-3xl opacity-80">📥</div>
        </div>
    </div>

    <!-- Sorties période -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Sorties</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($sorties_periode ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Sur la période</div>
            </div>
            <div class="text-3xl opacity-80">📤</div>
        </div>
    </div>
</div>

<!-- Alertes stock -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Produits en rupture -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">🚨 Ruptures de stock</h3>
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($produits_rupture ?? []) }}
            </span>
        </div>
        @if(isset($produits_rupture) && count($produits_rupture) > 0)
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach($produits_rupture as $produit)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-red-600 mt-1">Catégorie: {{ ucfirst($produit->product->categorie ?? 'N/A') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs font-semibold text-red-600">Stock: 0</div>
                        <div class="text-xs text-gray-500">Seuil: {{ $produit->seuil ?? 'N/A' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @can('create-movements')
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('movements.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    ➕ Réapprovisionner
                </a>
            </div>
            @endcan
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">✅</div>
                <div class="text-sm font-medium">Aucune rupture de stock</div>
                <div class="text-xs mt-1">Tous les produits sont disponibles</div>
            </div>
        @endif
    </div>

    <!-- Produits en alerte -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">⚠️ Stock faible</h3>
            <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($produits_alerte ?? []) }}
            </span>
        </div>
        @if(isset($produits_alerte) && count($produits_alerte) > 0)
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach($produits_alerte as $produit)
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-orange-600 mt-1">Catégorie: {{ ucfirst($produit->product->categorie ?? 'N/A') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs font-semibold text-orange-600">{{ $produit->quantite ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Seuil: {{ $produit->seuil ?? 'N/A' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @can('create-movements')
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('movements.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                    ➕ Réapprovisionner
                </a>
            </div>
            @endcan
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">✅</div>
                <div class="text-sm font-medium">Tous les stocks sont OK</div>
                <div class="text-xs mt-1">Aucun produit sous le seuil d'alerte</div>
            </div>
        @endif
    </div>
</div>

<!-- Analyses produits -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Top produits en stock -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Produits avec le plus de stock</h3>
        @if(isset($top_produits_stock) && count($top_produits_stock) > 0)
            <div class="space-y-3">
                @foreach($top_produits_stock as $stock)
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $stock->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $stock->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-blue-600 mt-1">{{ ucfirst($stock->product->categorie ?? 'N/A') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-blue-600">{{ $stock->quantite ?? 0 }}</div>
                        <div class="text-xs text-gray-500">unités</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">📊</div>
                <div class="text-sm">Aucune donnée disponible</div>
            </div>
        @endif
    </div>

    <!-- Stock par catégorie -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🏷️ Stock par catégorie</h3>
        @if(isset($categories_stock) && count($categories_stock) > 0)
            <div class="space-y-3">
                @foreach($categories_stock as $categorie)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-green-500 mr-3"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ ucfirst($categorie->categorie ?? 'Non catégorisé') }}</div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900">
                        {{ number_format($categorie->total_stock ?? 0, 0, ',', ' ') }} unités
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">🏷️</div>
                <div class="text-sm">Aucune donnée disponible</div>
            </div>
        @endif
    </div>
</div>

