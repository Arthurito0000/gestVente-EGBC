@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Produits</h1>
      <p class="text-gray-500">Gérez le catalogue produits (SKU, prix, etc.).</p>
    </div>
    <div class="flex items-center gap-3">
      <div class="relative">
        <input type="text" id="searchInput" placeholder="Rechercher par SKU, nom ou catégorie..." class="w-80 rounded-lg border border-gray-300 focus:outline-none pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <div id="searchSpinner" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
          <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
      </div>
      @can('create-products')
      <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 whitespace-nowrap">
        ➕ Nouveau produit
      </a>
      @endcan
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des produits</div>
      <div class="flex items-center gap-2 text-sm">
        @can('export-products')
        <!-- Bouton Export Excel -->
        <button id="exportExcelBtn" class="flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors" title="Exporter en Excel">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M15.5,13L13.5,17H12L14.5,12.5L12,8H13.5L15.5,12L17.5,8H19L16.5,12.5L19,17H17.5L15.5,13Z" fill="white"/>
          </svg>
          Excel
        </button>
        
        <!-- Bouton Export PDF -->
        <button id="exportPdfBtn" class="flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors" title="Exporter en PDF">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <text x="7" y="16" font-family="Arial" font-size="6" fill="white" font-weight="bold">PDF</text>
          </svg>
          PDF
        </button>
        @endcan
      </div>
    </div>
    <div id="tableContainer">
      @include('products.partials.table', ['products' => $products])
    </div>
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
// Variables globales pour la recherche (accessibles partout)
let searchTimeout;
let currentSearch = '';

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const tableContainer = document.getElementById('tableContainer');

    // Fonction de recherche AJAX
    function performSearch(query) {
        currentSearch = query;
        
        // Afficher le spinner
        searchSpinner.classList.remove('hidden');
        
        // Construire l'URL avec les paramètres
        const url = new URL(window.location.href);
        if (query.trim()) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
        }
        
        // Requête AJAX
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Mettre à jour le contenu de la table
            tableContainer.innerHTML = html;
            
            // Réattacher les événements de suppression
            attachDeleteEvents();
            
            // Réattacher les événements de pagination
            attachPaginationEvents();
            
            // Masquer le spinner
            searchSpinner.classList.add('hidden');
            
            // Mettre à jour l'URL sans recharger la page
            window.history.pushState({}, '', url.toString());
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error);
            searchSpinner.classList.add('hidden');
        });
    }

    // Événement de saisie dans le champ de recherche
    searchInput.addEventListener('input', function() {
        const query = this.value;
        
        // Annuler la recherche précédente
        clearTimeout(searchTimeout);
        
        // Lancer une nouvelle recherche après 300ms
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Fonction pour attacher les événements de pagination
    function attachPaginationEvents() {
        const paginationLinks = document.querySelectorAll('#pagination-container a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = new URL(this.href);
                // Ajouter le terme de recherche actuel
                if (currentSearch.trim()) {
                    url.searchParams.set('search', currentSearch);
                }
                
                // Afficher le spinner
                searchSpinner.classList.remove('hidden');
                
                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    attachDeleteEvents();
                    attachPaginationEvents();
                    searchSpinner.classList.add('hidden');
                    window.history.pushState({}, '', url.toString());
                })
                .catch(error => {
                    console.error('Erreur lors de la pagination:', error);
                    searchSpinner.classList.add('hidden');
                });
            });
        });
    }

    // Fonction pour attacher les événements de suppression
    function attachDeleteEvents() {
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
    }

    // Initialiser les événements de pagination au chargement
    attachPaginationEvents();

    // Attacher les événements d'exportation
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            console.log('Bouton Excel cliqué via event listener');
            exportExcel();
        });
    }

    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function() {
            console.log('Bouton PDF cliqué via event listener');
            exportPdf();
        });
    }
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

// Fonctions d'exportation
function exportExcel() {
    try {
        console.log('Fonction exportExcel appelée'); // Debug
        
        // Récupérer la valeur actuelle du champ de recherche
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const searchParam = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
        
        const url = `{{ route('products.export.excel') }}${searchParam}`;
        console.log('URL Excel:', url); // Debug
        
        window.location.href = url;
    } catch (error) {
        console.error('Erreur lors de l\'export Excel:', error);
        alert('Erreur lors de l\'exportation Excel');
    }
}

function exportPdf() {
    try {
        console.log('Fonction exportPdf appelée'); // Debug
        
        // Récupérer la valeur actuelle du champ de recherche
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const searchParam = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
        
        const url = `{{ route('products.export.pdf') }}${searchParam}`;
        console.log('URL PDF:', url); // Debug
        
        window.open(url, '_blank');
    } catch (error) {
        console.error('Erreur lors de l\'export PDF:', error);
        alert('Erreur lors de l\'exportation PDF');
    }
}
</script>
@endsection
