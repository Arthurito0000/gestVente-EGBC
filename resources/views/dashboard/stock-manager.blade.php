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

<!-- Produits invendus -->
@if(isset($produits_invendus) && count($produits_invendus) > 0)
<div class="bg-white rounded-2xl shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">😴 Produits invendus sur la période</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($produits_invendus->take(6) as $produit)
        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="text-sm font-medium text-gray-900">{{ $produit->sku ?? 'N/A' }}</div>
            <div class="text-xs text-gray-600 mt-1">{{ $produit->nom ?? 'N/A' }}</div>
            <div class="text-xs text-yellow-600 mt-2">{{ ucfirst($produit->categorie ?? 'N/A') }}</div>
            <div class="text-xs text-gray-500 mt-1">
                Stock: {{ $produit->stock->quantite ?? 0 }} | 
                Prix: {{ number_format($produit->prix_achat ?? 0, 0, ',', ' ') }} Fcfa
            </div>
        </div>
        @endforeach
    </div>
    @if(count($produits_invendus) > 6)
    <div class="mt-4 text-center">
        <span class="text-sm text-gray-500">Et {{ count($produits_invendus) - 6 }} autres produits invendus...</span>
    </div>
    @endif
</div>
@endif

<!-- Mouvements récents -->
<div class="bg-white rounded-2xl shadow-md">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">📋 Mouvements récents</h3>
        <div class="flex gap-2">
            @can('view-movements')
            <a href="{{ route('movements.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir tout</a>
            @endcan
            @can('create-movements')
            <a href="{{ route('movements.create') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                ➕ Nouveau mouvement
            </a>
            @endcan
        </div>
    </div>
    <div class="overflow-x-auto">
        @if(isset($mouvements_recents) && count($mouvements_recents) > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Prix d'achat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($mouvements_recents as $mouvement)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $mouvement->date ? $mouvement->date->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $mouvement->product->sku ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-600">{{ $mouvement->product->nom ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $mouvement->type === 'ENTREE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $mouvement->type ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium 
                        {{ $mouvement->type === 'ENTREE' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $mouvement->type === 'ENTREE' ? '+' : '-' }}{{ $mouvement->quantite ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $mouvement->motif ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                        @if($mouvement->prix_achat)
                            {{ number_format($mouvement->prix_achat, 0, ',', ' ') }} Fcfa
                            <div class="text-xs text-green-600">Prix mis à jour</div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12 text-gray-500">
            <div class="text-4xl mb-4">📋</div>
            <div class="text-lg font-medium">Aucun mouvement récent</div>
            <div class="text-sm mt-1">Les mouvements de stock apparaîtront ici</div>
            @can('create-movements')
            <div class="mt-4">
                <a href="{{ route('movements.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    ➕ Créer le premier mouvement
                </a>
            </div>
            @endcan
        </div>
        @endif
    </div>
</div>
