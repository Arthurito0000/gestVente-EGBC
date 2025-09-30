@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">💰 Gestion des ventes</h1>
                <p class="text-gray-600 mt-1">Liste de toutes les ventes enregistrées</p>
            </div>
            @can('create-sales')
            <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                ➕ Nouvelle vente
            </a>
            @endcan
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Liste des ventes -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        @if($sales->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Facture
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produit
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vendeur
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantité
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Prix unitaire
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sale->numero_facture }}</div>
                            @if($sale->notes)
                            <div class="text-xs text-gray-500">{{ Str::limit($sale->notes, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $sale->date_vente ? \Carbon\Carbon::parse($sale->date_vente)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sale->product->nom ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->product->sku ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $sale->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            {{ $sale->quantite }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                            {{ number_format($sale->prix_unitaire, 0, ',', ' ') }} Fcfa
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                            {{ number_format($sale->total, 0, ',', ' ') }} Fcfa
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                @can('view-sales')
                                <a href="{{ route('sales.show', $sale) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Voir les détails">
                                    👁️
                                </a>
                                @endcan
                                
                                @can('edit-sales')
                                <a href="{{ route('sales.edit', $sale) }}" 
                                   class="text-yellow-600 hover:text-yellow-900" title="Modifier">
                                    ✏️
                                </a>
                                @endcan
                                
                                @can('delete-sales')
                                <form method="POST" action="{{ route('sales.destroy', $sale) }}" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ? Le stock sera restauré.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" title="Supprimer">
                                        🗑️
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sales->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-4xl mb-4">💰</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune vente enregistrée</h3>
            <p class="text-gray-600 mb-4">Commencez par enregistrer votre première vente</p>
            @can('create-sales')
            <a href="{{ route('sales.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                ➕ Créer la première vente
            </a>
            @endcan
        </div>
        @endif
    </div>

    <!-- Statistiques rapides -->
    @if($sales->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-green-50 rounded-2xl p-6 border border-green-200">
            <div class="flex items-center">
                <div class="text-2xl mr-3">💰</div>
                <div>
                    <div class="text-sm text-green-600 font-medium">Total des ventes</div>
                    <div class="text-lg font-bold text-green-800">
                        {{ number_format($sales->sum('total'), 0, ',', ' ') }} Fcfa
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
            <div class="flex items-center">
                <div class="text-2xl mr-3">📦</div>
                <div>
                    <div class="text-sm text-blue-600 font-medium">Produits vendus</div>
                    <div class="text-lg font-bold text-blue-800">
                        {{ $sales->sum('quantite') }} unités
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-purple-50 rounded-2xl p-6 border border-purple-200">
            <div class="flex items-center">
                <div class="text-2xl mr-3">📊</div>
                <div>
                    <div class="text-sm text-purple-600 font-medium">Panier moyen</div>
                    <div class="text-lg font-bold text-purple-800">
                        {{ $sales->count() > 0 ? number_format($sales->sum('total') / $sales->count(), 0, ',', ' ') : 0 }} Fcfa
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
