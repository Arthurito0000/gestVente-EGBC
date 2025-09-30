<!-- Dashboard Basique - Vue limitée -->

<!-- Message de bienvenue -->
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-md p-8 text-white text-center mb-6">
    <div class="text-4xl mb-4">👋</div>
    <h2 class="text-2xl font-bold mb-2">Bienvenue dans GestVentes EGBC</h2>
    <p class="opacity-90">Votre tableau de bord personnalisé</p>
</div>

<!-- KPIs basiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total produits -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Total produits</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total_produits ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs text-gray-500 mt-1">Dans le catalogue</div>
            </div>
            <div class="text-3xl text-blue-500">📦</div>
        </div>
    </div>

    <!-- Produits en alerte -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Stock faible</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ count($produits_alerte ?? []) }}</div>
                <div class="text-xs text-gray-500 mt-1">Produits en alerte</div>
            </div>
            <div class="text-3xl text-orange-500">⚠️</div>
        </div>
    </div>

    <!-- Mouvements récents -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Activité récente</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ count($mouvements_recents ?? []) }}</div>
                <div class="text-xs text-gray-500 mt-1">Mouvements récents</div>
            </div>
            <div class="text-3xl text-green-500">📋</div>
        </div>
    </div>
</div>

<!-- Alertes stock -->
@if(isset($produits_alerte) && count($produits_alerte) > 0)
<div class="bg-white rounded-2xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">⚠️ Produits en alerte</h3>
        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
            {{ count($produits_alerte) }}
        </span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($produits_alerte->take(6) as $produit)
        <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
            <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
            <div class="text-xs text-gray-600 mt-1">{{ $produit->product->nom ?? 'N/A' }}</div>
            <div class="text-xs text-orange-600 mt-2">
                Stock: {{ $produit->quantite ?? 0 }} / Seuil: {{ $produit->seuil ?? 'N/A' }}
            </div>
        </div>
        @endforeach
    </div>
    @if(count($produits_alerte) > 6)
    <div class="mt-4 text-center">
        <span class="text-sm text-gray-500">Et {{ count($produits_alerte) - 6 }} autres produits en alerte...</span>
    </div>
    @endif
</div>
@endif

<!-- Mouvements récents -->
<div class="bg-white rounded-2xl shadow-md">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">📋 Activité récente</h3>
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
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-12 text-gray-500">
            <div class="text-4xl mb-4">📋</div>
            <div class="text-lg font-medium">Aucune activité récente</div>
            <div class="text-sm mt-1">Les mouvements apparaîtront ici</div>
        </div>
        @endif
    </div>
</div>

<!-- Message d'information -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6">
    <div class="flex items-start">
        <div class="text-2xl mr-3">ℹ️</div>
        <div>
            <h4 class="text-lg font-semibold text-blue-900 mb-2">Accès limité</h4>
            <p class="text-blue-800 text-sm">
                Votre compte a un accès limité au système. Pour accéder à plus de fonctionnalités, 
                contactez votre administrateur pour obtenir les permissions appropriées.
            </p>
        </div>
    </div>
</div>
