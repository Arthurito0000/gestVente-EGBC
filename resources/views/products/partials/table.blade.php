<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix Achat</th>
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Catégorie</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
      @forelse($products as $product)
      <tr class="hover:bg-gray-50">
        <td class="px-2 py-1 text-sm font-mono text-gray-700">{{ $product->sku }}</td>
        <td class="px-2 py-1 text-sm text-gray-900">{{ $product->nom }}</td>
        <td class="px-2 py-1 text-sm text-right text-gray-700">{{ number_format($product->prix_achat, 2, ',', ' ') }} Fcfa</td>
        <td class="px-2 py-1 text-sm text-right text-gray-700">
          @if($product->categorie)
            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ ucfirst($product->categorie) }}</span>
          @else
            <span class="px-2 py-1 text-xs rounded-full bg-gray-50 text-gray-400">Non définie</span>
          @endif
        </td>
        <td class="px-2 py-1 text-sm text-right">
          <div class="flex items-center justify-end gap-2">
            <!-- Voir -->
            <a href="{{ route('products.show', $product) }}" class="p-2 text-primary-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Voir le produit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </a>
            
            <!-- Modifier -->
            <a href="{{ route('products.edit', $product) }}" class="p-2 text-blue-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier le produit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </a>
            
            <!-- Supprimer -->
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline delete-form">
              @csrf
              @method('DELETE')
              <button type="button" class="delete-btn p-2 text-red-700 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer le produit" data-product-name="{{ $product->nom }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
          Aucun produit trouvé. <a href="{{ route('products.create') }}" class="text-primary-700 hover:text-primary-600">Créer le premier produit</a>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Pagination -->
@if($products->hasPages())
  <div class="px-6 py-4 border-t border-gray-200" id="pagination-container">
    <style>
      /* Styles personnalisés pour la pagination bleue */
      .pagination .page-link {
        color: #3b82f6 !important;
        border-color: #e5e7eb !important;
      }
      .pagination .page-link:hover {
        color: #1d4ed8 !important;
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
      }
      .pagination .page-item.active .page-link {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
      }
      .pagination .page-item.disabled .page-link {
        color: #9ca3af !important;
        background-color: #f9fafb !important;
        border-color: #e5e7eb !important;
      }
    </style>
    {{ $products->links() }}
  </div>
@endif
