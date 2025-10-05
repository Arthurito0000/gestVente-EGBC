<!-- Dashboard Vendeur - Statistiques globales simplifiées -->

<!-- KPIs Ventes globales -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
    <!-- Nombre de ventes -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Nombre de ventes</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($nombre_ventes ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Sur la période</div>
            </div>
            <div class="text-3xl opacity-80">🛒</div>
        </div>
    </div>

    <!-- Chiffre d'affaires -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Chiffre d'affaires</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($chiffre_affaires ?? 0, 0, ',', ' ') }} Fcfa</div>
                <div class="text-xs mt-1 opacity-90">Total des ventes</div>
            </div>
            <div class="text-3xl opacity-80">💰</div>
        </div>
    </div>

    <!-- Produits vendus -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Produits vendus</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($produits_vendus ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Quantité totale</div>
            </div>
            <div class="text-3xl opacity-80">📦</div>
        </div>
    </div>
</div>

<!-- Alertes et informations -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Produits en alerte (à éviter de vendre) -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">⚠️ Attention - Stock faible</h3>
            <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($produits_alerte ?? []) }}
            </span>
        </div>
        @if(isset($produits_alerte) && count($produits_alerte) > 0)
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach($produits_alerte->take(8) as $produit)
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-orange-600 mt-1">⚠️ Éviter de vendre - Stock critique</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs font-semibold text-orange-600">{{ $produit->quantite ?? 0 }}</div>
                        <div class="text-xs text-gray-500">restant</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">✅</div>
                <div class="text-sm font-medium">Tous les stocks sont OK</div>
                <div class="text-xs mt-1">Vous pouvez vendre tous les produits</div>
            </div>
        @endif
    </div>

    <!-- Produits disponibles -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📦 Produits disponibles</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($produits_disponibles ?? []) }}
            </span>
        </div>
        @if(isset($produits_disponibles) && count($produits_disponibles) > 0)
            <div class="space-y-2 max-h-60 overflow-y-auto">
                @foreach($produits_disponibles->take(8) as $stock)
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $stock->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $stock->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-blue-600 mt-1">
                            {{ ucfirst($stock->product->categorie ?? 'N/A') }}
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-blue-600">{{ $stock->quantite ?? 0 }}</div>
                        <div class="text-xs text-gray-500">disponibles</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">📦</div>
                <div class="text-sm">Aucun produit en stock</div>
            </div>
        @endif
    </div>
</div>

<!-- Actions rapides -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    @can('create-sales')
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-md p-6 text-white">
        <div class="text-center">
            <div class="text-3xl mb-2">💰</div>
            <h3 class="text-lg font-semibold mb-2">Nouvelle facture</h3>
            <p class="text-sm opacity-90 mb-4">Créer une nouvelle facture</p>
            <a href="{{ route('invoices.create') }}" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Créer une facture
            </a>
        </div>
    </div>
    @endcan

    @can('view-products')
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-md p-6 text-white">
        <div class="text-center">
            <div class="text-3xl mb-2">📋</div>
            <h3 class="text-lg font-semibold mb-2">Consulter produits</h3>
            <p class="text-sm opacity-90 mb-4">Voir les produits disponibles</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Voir les produits
            </a>
        </div>
    </div>
    @endcan

    @can('view-sales')
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-md p-6 text-white">
        <div class="text-center">
            <div class="text-3xl mb-2">📊</div>
            <h3 class="text-lg font-semibold mb-2">Factures</h3>
            <p class="text-sm opacity-90 mb-4">Historique des factures</p>
            <a href="{{ route('invoices.index') }}" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Voir les factures
            </a>
        </div>
    </div>
    @endcan
</div>
