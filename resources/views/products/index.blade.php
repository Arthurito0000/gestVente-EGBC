@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Produits</h1>
      <p class="text-gray-500">Gérez le catalogue produits (SKU, prix, etc.).</p>
    </div>
    <div class="flex items-center gap-3">
      <input type="text" placeholder="Rechercher…" class="hidden md:block rounded-lg border border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500" />
      <a href="{{ route('products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouveau produit</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des produits</div>
      <div class="flex items-center gap-2 text-sm">
        <button class="px-3 py-1 rounded border">Exporter</button>
      </div>
    </div>
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
      <div class="px-6 py-4 border-t border-gray-200">
        {{ $products->links() }}
      </div>
    @endif
  </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm transition-all duration-300">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div id="modalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
      <!-- Bouton de fermeture -->
      <button id="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
      
      <div class="p-8 text-center">
        <!-- Icône d'alerte animée -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6 animate-pulse">
          <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        
        <!-- Titre -->
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Confirmer la suppression</h3>
        
        <!-- Message -->
        <div class="mb-8">
          <p class="text-gray-600 mb-2">
            Êtes-vous sûr de vouloir supprimer le produit
          </p>
          <p class="font-semibold text-gray-900 text-lg" id="productName"></p>
          <p class="text-sm text-red-600 mt-3 bg-red-50 px-4 py-2 rounded-lg">
            ⚠️ Cette action est irréversible
          </p>
        </div>
        
        <!-- Boutons -->
        <div class="flex gap-3">
          <button id="cancelDelete" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 hover:scale-105">
            Annuler
          </button>
          <button id="confirmDelete" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 hover:scale-105 shadow-lg">
            Supprimer
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    const modalContent = document.getElementById('modalContent');
    const productNameSpan = document.getElementById('productName');
    const cancelBtn = document.getElementById('cancelDelete');
    const confirmBtn = document.getElementById('confirmDelete');
    const closeBtn = document.getElementById('closeModal');
    let currentForm = null;

    // Gestion de la suppression avec modal
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const productName = this.getAttribute('data-product-name');
            currentForm = this.closest('.delete-form');
            
            // Mettre à jour le contenu du modal
            productNameSpan.textContent = productName;
            
            // Afficher le modal avec animation
            showModal();
        });
    });

    // Fermer le modal - Annuler
    cancelBtn.addEventListener('click', function() {
        closeModal();
    });

    // Fermer le modal - X
    closeBtn.addEventListener('click', function() {
        closeModal();
    });

    // Fermer le modal en cliquant sur l'arrière-plan
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Confirmer la suppression
    confirmBtn.addEventListener('click', function() {
        if (currentForm) {
            // Animation de chargement
            confirmBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            confirmBtn.disabled = true;
            
            setTimeout(() => {
                currentForm.submit();
            }, 500);
        }
    });

    function showModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Animation d'entrée
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        // Animation de sortie
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            currentForm = null;
            
            // Réinitialiser le bouton de confirmation
            confirmBtn.innerHTML = 'Supprimer';
            confirmBtn.disabled = false;
        }, 300);
    }
});
</script>
@endsection
