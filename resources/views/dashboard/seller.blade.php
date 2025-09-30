<!-- Dashboard Vendeur - Focus ventes personnelles -->

<!-- KPIs Ventes personnelles -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- Mes ventes -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Mes ventes</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($mes_ventes ?? 0, 0, ',', ' ') }}</div>
                @if(isset($mes_ventes_precedent))
                    @php
                        $evolution_ventes = $mes_ventes_precedent > 0 ? (($mes_ventes - $mes_ventes_precedent) / $mes_ventes_precedent) * 100 : 0;
                    @endphp
                    <div class="text-xs mt-1 opacity-90">
                        {{ $evolution_ventes >= 0 ? '+' : '' }}{{ number_format($evolution_ventes, 1) }}% vs période précédente
                    </div>
                @endif
            </div>
            <div class="text-3xl opacity-80">🛒</div>
        </div>
    </div>

    <!-- Mon CA -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Mon chiffre d'affaires</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($mon_ca ?? 0, 0, ',', ' ') }} Fcfa</div>
                <div class="text-xs mt-1 opacity-90">Sur la période</div>
            </div>
            <div class="text-3xl opacity-80">💰</div>
        </div>
    </div>

    <!-- Ma position -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Mon classement</div>
                <div class="text-2xl font-bold mt-1">
                    @if(isset($ma_position) && $ma_position)
                        #{{ $ma_position }}
                        @if($ma_position === 1) 🥇
                        @elseif($ma_position === 2) 🥈
                        @elseif($ma_position === 3) 🥉
                        @endif
                    @else
                        -
                    @endif
                </div>
                <div class="text-xs mt-1 opacity-90">Parmi les vendeurs</div>
            </div>
            <div class="text-3xl opacity-80">🏆</div>
        </div>
    </div>

    <!-- Produits disponibles -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Produits disponibles</div>
                <div class="text-2xl font-bold mt-1">{{ count($produits_disponibles ?? []) }}</div>
                <div class="text-xs mt-1 opacity-90">En stock</div>
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

    <!-- Classement des vendeurs -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">🏆 Classement vendeurs</h3>
        </div>
        @if(isset($classement_vendeurs) && count($classement_vendeurs) > 0)
            <div class="space-y-3">
                @foreach($classement_vendeurs->take(5) as $index => $vendeur)
                <div class="flex items-center justify-between p-3 rounded-lg 
                    {{ $vendeur->user_id === auth()->id() ? 'bg-blue-100 border-2 border-blue-300' : 'bg-gray-50' }}">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }} flex items-center justify-center text-sm font-bold mr-3">
                            {{ $index + 1 }}
                            @if($index === 0) 🥇
                            @elseif($index === 1) 🥈
                            @elseif($index === 2) 🥉
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $vendeur->user->name ?? 'N/A' }}
                                @if($vendeur->user_id === auth()->id())
                                    <span class="text-blue-600 font-bold">(Vous)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-green-600">
                        {{ number_format($vendeur->ca_vendeur ?? 0, 0, ',', ' ') }} Fcfa
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">🏆</div>
                <div class="text-sm">Aucune vente sur la période</div>
            </div>
        @endif
    </div>
</div>

<!-- Mes top produits et produits disponibles -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Mes produits les plus vendus -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🥇 Mes produits les plus vendus</h3>
        @if(isset($mes_top_produits) && count($mes_top_produits) > 0)
            <div class="space-y-3">
                @foreach($mes_top_produits as $produit)
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-green-600 mt-1">{{ ucfirst($produit->product->categorie ?? 'N/A') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-green-600">{{ $produit->total_vendu ?? 0 }}</div>
                        <div class="text-xs text-gray-500">vendues</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">🛒</div>
                <div class="text-sm font-medium">Aucune vente sur la période</div>
                <div class="text-xs mt-1">Commencez à vendre pour voir vos statistiques</div>
            </div>
        @endif
    </div>

    <!-- Produits avec le plus de stock (à privilégier) -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Produits à privilégier (stock élevé)</h3>
        @if(isset($produits_disponibles) && count($produits_disponibles) > 0)
            <div class="space-y-3">
                @foreach($produits_disponibles->take(8) as $stock)
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $stock->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $stock->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-blue-600 mt-1">
                            {{ ucfirst($stock->product->categorie ?? 'N/A') }} • 
                            Prix: {{ number_format($stock->product->prix_achat ?? 0, 0, ',', ' ') }} Fcfa
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-blue-600">{{ $stock->quantite ?? 0 }}</div>
                        <div class="text-xs text-gray-500">disponibles</div>
                    </div>
                </div>
                @endforeach
            </div>
            @can('view-products')
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Voir tous les produits →
                </a>
            </div>
            @endcan
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
            <h3 class="text-lg font-semibold mb-2">Nouvelle vente</h3>
            <p class="text-sm opacity-90 mb-4">Enregistrer une nouvelle vente</p>
            <a href="{{ route('sales.create') }}" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Créer une vente
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
            <h3 class="text-lg font-semibold mb-2">Mes ventes</h3>
            <p class="text-sm opacity-90 mb-4">Historique de mes ventes</p>
            <a href="{{ route('sales.index') }}" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                Voir l'historique
            </a>
        </div>
    </div>
    @endcan
</div>

<!-- Conseils et astuces -->
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-md p-6 text-white mb-6">
    <h3 class="text-lg font-semibold mb-3">💡 Conseils pour optimiser vos ventes</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div class="flex items-start">
            <div class="text-lg mr-2">🎯</div>
            <div>
                <div class="font-medium">Privilégiez les produits avec beaucoup de stock</div>
                <div class="opacity-90">Évitez de vendre les produits en alerte stock</div>
            </div>
        </div>
        <div class="flex items-start">
            <div class="text-lg mr-2">📈</div>
            <div>
                <div class="font-medium">Suivez votre classement</div>
                <div class="opacity-90">Comparez vos performances avec les autres vendeurs</div>
            </div>
        </div>
        <div class="flex items-start">
            <div class="text-lg mr-2">🏆</div>
            <div>
                <div class="font-medium">Concentrez-vous sur vos produits stars</div>
                <div class="opacity-90">Vendez plus de vos produits les plus performants</div>
            </div>
        </div>
        <div class="flex items-start">
            <div class="text-lg mr-2">⚡</div>
            <div>
                <div class="font-medium">Utilisez les actions rapides</div>
                <div class="opacity-90">Créez rapidement de nouvelles ventes</div>
            </div>
        </div>
    </div>
</div>
