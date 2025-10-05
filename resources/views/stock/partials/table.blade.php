<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Catégorie</th>
        @if(auth()->user()->hasRole(['Administrateur', 'Gérant de Stock']))
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix d'achat</th>
        @endif
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qte Stock</th>
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Seuil</th>
        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
        @if(auth()->user()->hasRole(['Administrateur', 'Gérant de Stock']))
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valeur Stock</th>
        @endif
        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
      @forelse($stocks as $stock)
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-2 text-sm text-gray-900">{{ $stock->product->nom }}</td>
        <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $stock->product->sku }}</td>
        <td class="px-4 py-2 text-sm text-center">
          @if($stock->product->categorie)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ ucfirst($stock->product->categorie) }}
            </span>
          @else
            <span class="text-gray-400">Non définie</span>
          @endif
        </td>
        @if(auth()->user()->hasRole(['Administrateur', 'Gérant de Stock']))
        <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">
          {{ number_format($stock->product->prix_achat, 0, ',', ' ') }} Fcfa
        </td>
        @endif
        <td class="px-4 py-2 text-sm text-right font-medium {{ $stock->quantite <= $stock->seuil ? ($stock->quantite == 0 ? 'text-red-600' : 'text-orange-600') : 'text-gray-900' }}">
          {{ $stock->quantite }}
        </td>
        <td class="px-4 py-2 text-sm text-right text-gray-700">{{ $stock->seuil }}</td>
        <td class="px-4 py-2 text-center">
          @if($stock->quantite == 0)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
              Rupture
            </span>
          @elseif($stock->quantite <= $stock->seuil)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
              Stock faible
            </span>
          @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              Normal
            </span>
          @endif
        </td>
        @if(auth()->user()->hasRole(['Administrateur', 'Gérant de Stock']))
        <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">
          {{ number_format($stock->quantite * $stock->product->prix_achat, 0, ',', ' ') }} Fcfa
        </td>
        @endif
        <td class="px-4 py-2 text-center">
          <!-- Supprimer le stock -->
          <button onclick="confirmDeleteStock({{ $stock->id }}, '{{ $stock->product->nom }}')" 
                  class="p-2.5 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
                  title="Supprimer le stock">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="{{ auth()->user()->hasRole(['Administrateur', 'Gérant de Stock']) ? '9' : '7' }}" class="px-4 py-8 text-center text-gray-500">
          @if(request('search'))
            Aucun stock trouvé pour "{{ request('search') }}". 
            <a href="{{ route('stock.index') }}" class="text-primary-700 hover:text-primary-600">Voir tous les stocks</a>
          @else
            Aucun stock trouvé. <a href="{{ route('products.create') }}" class="text-primary-700 hover:text-primary-600">Créer le premier produit</a>
          @endif
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Pagination -->
<div id="pagination-container">
  @if($stocks->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Affichage de {{ $stocks->firstItem() }} à {{ $stocks->lastItem() }} sur {{ $stocks->total() }} résultats
        </div>
        <div class="pagination-links">
          {{ $stocks->links('vendor.pagination.simple-tailwind') }}
        </div>
      </div>
    </div>
  @endif
</div>

