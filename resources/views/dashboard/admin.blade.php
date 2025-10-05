<!-- Dashboard Administrateur - Vue complète -->

<!-- KPIs Financiers -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- Chiffre d'affaires -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Chiffre d'affaires</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($chiffre_affaires ?? 0, 0, ',', ' ') }} Fcfa</div>
                @if(isset($chiffre_affaires_precedent) && $chiffre_affaires_precedent > 0)
                    @php
                        $evolution_ca = (($chiffre_affaires - $chiffre_affaires_precedent) / $chiffre_affaires_precedent) * 100;
                    @endphp
                    <div class="text-xs mt-1 opacity-90">
                        {{ $evolution_ca >= 0 ? '+' : '' }}{{ number_format($evolution_ca, 1) }}% vs période précédente
                    </div>
                @endif
            </div>
            <div class="text-3xl opacity-80">💰</div>
        </div>
    </div>

    <!-- Bénéfice net -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Bénéfice net</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($benefice_net ?? 0, 0, ',', ' ') }} Fcfa</div>
                @if(isset($benefice_precedent) && $benefice_precedent > 0)
                    @php
                        $evolution_benefice = (($benefice_net - $benefice_precedent) / $benefice_precedent) * 100;
                    @endphp
                    <div class="text-xs mt-1 opacity-90">
                        {{ $evolution_benefice >= 0 ? '+' : '' }}{{ number_format($evolution_benefice, 1) }}% vs période précédente
                    </div>
                @endif
            </div>
            <div class="text-3xl opacity-80">📈</div>
        </div>
    </div>

    <!-- Produits vendus -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Produits vendus</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($produits_vendus ?? 0, 0, ',', ' ') }}</div>
                <div class="text-xs mt-1 opacity-90">Sur {{ number_format($total_produits ?? 0, 0, ',', ' ') }} produits</div>
            </div>
            <div class="text-3xl opacity-80">📦</div>
        </div>
    </div>

    <!-- Valeur stock -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl shadow-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90">Valeur du stock</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($valeur_stock ?? 0, 0, ',', ' ') }} Fcfa</div>
                <div class="text-xs mt-1 opacity-90">Inventaire total</div>
            </div>
            <div class="text-3xl opacity-80">🏪</div>
        </div>
    </div>
</div>

<!-- Alertes et statuts -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Produits en rupture -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">🚨 Ruptures de stock</h3>
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ count($produits_rupture ?? []) }}
            </span>
        </div>
        @if(isset($produits_rupture) && count($produits_rupture) > 0)
            <div class="space-y-2 max-h-40 overflow-y-auto">
                @foreach($produits_rupture->take(5) as $produit)
                <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="text-xs font-semibold text-red-600">Stock: 0</div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-gray-500">
                <div class="text-2xl mb-2">✅</div>
                <div class="text-sm">Aucune rupture de stock</div>
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
            <div class="space-y-2 max-h-40 overflow-y-auto">
                @foreach($produits_alerte->take(5) as $produit)
                <div class="flex items-center justify-between p-2 bg-orange-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->product->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="text-xs font-semibold text-orange-600">{{ $produit->quantite ?? 0 }}</div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-gray-500">
                <div class="text-2xl mb-2">✅</div>
                <div class="text-sm">Tous les stocks sont OK</div>
            </div>
        @endif
    </div>

    <!-- Statistiques générales -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📈 Statistiques générales</h3>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-gray-900">Nombre total de ventes</div>
                    <div class="text-xs text-gray-600">Sur la période sélectionnée</div>
                </div>
                <div class="text-lg font-semibold text-blue-600">
                    {{ isset($top_vendeurs) ? $top_vendeurs->sum('nb_ventes') : 0 }}
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-gray-900">Nombre de vendeurs actifs</div>
                    <div class="text-xs text-gray-600">Ayant réalisé au moins une vente</div>
                </div>
                <div class="text-lg font-semibold text-green-600">
                    {{ isset($top_vendeurs) ? count($top_vendeurs) : 0 }}
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-gray-900">CA moyen par vente</div>
                    <div class="text-xs text-gray-600">Panier moyen</div>
                </div>
                <div class="text-lg font-semibold text-purple-600">
                    @if(isset($top_vendeurs) && $top_vendeurs->sum('nb_ventes') > 0)
                        {{ number_format(($chiffre_affaires ?? 0) / $top_vendeurs->sum('nb_ventes'), 0, ',', ' ') }} Fcfa
                    @else
                        0 Fcfa
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques et analyses -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Top produits -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🥇 Produits les plus vendus</h3>
        @if(isset($top_produits) && count($top_produits) > 0)
            <div class="space-y-3">
                @foreach($top_produits as $produit)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $produit->product->sku ?? 'N/A' }} - {{ $produit->product->nom ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">{{ $produit->total_vendu ?? 0 }} unités vendues</div>
                    </div>
                    <div class="text-sm font-semibold text-green-600">
                        {{ number_format($produit->ca_produit ?? 0, 0, ',', ' ') }} Fcfa
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">📈</div>
                <div class="text-sm">Aucune vente sur la période</div>
            </div>
        @endif
    </div>

    <!-- Ventes par catégorie -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🥧 Répartition par catégorie</h3>
        @if(isset($ventes_par_categorie) && count($ventes_par_categorie) > 0)
            <div class="space-y-3">
                @foreach($ventes_par_categorie as $categorie)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-blue-500 mr-3"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ ucfirst($categorie->categorie ?? 'Non catégorisé') }}</div>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-gray-900">
                        {{ number_format($categorie->ca_categorie ?? 0, 0, ',', ' ') }} Fcfa
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-3xl mb-2">🥧</div>
                <div class="text-sm">Aucune donnée disponible</div>
            </div>
        @endif
    </div>
</div>

