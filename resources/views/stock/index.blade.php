@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Stock</h1>
      <p class="text-gray-500">Suivi des niveaux de stock par produit.</p>
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
      <a href="{{ route('products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2 whitespace-nowrap">Nouveau produit</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
      <div class="text-sm text-gray-600">Niveaux actuels</div>
      <div class="flex items-center gap-2 text-sm">
        @can('export-stock')
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
        
        <!-- Bouton Inventaire -->
        <button id="inventaireBtn" class="flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" title="Fiche d'inventaire">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19M16.5,16.25C16.5,16.66 16.16,17 15.75,17H14.75V18A0.5,0.5 0 0,1 14.25,18.5H13.75A0.5,0.5 0 0,1 13.25,18V17H8.25C7.84,17 7.5,16.66 7.5,16.25V15.75C7.5,15.34 7.84,15 8.25,15H13.25V14A0.5,0.5 0 0,1 13.75,13.5H14.25A0.5,0.5 0 0,1 14.75,14V15H15.75C16.16,15 16.5,15.34 16.5,15.75V16.25M16.5,11.25C16.5,11.66 16.16,12 15.75,12H8.25C7.84,12 7.5,11.66 7.5,11.25V10.75C7.5,10.34 7.84,10 8.25,10H15.75C16.16,10 16.5,10.34 16.5,10.75V11.25M16.5,6.25C16.5,6.66 16.16,7 15.75,7H8.25C7.84,7 7.5,6.66 7.5,6.25V5.75C7.5,5.34 7.84,5 8.25,5H15.75C16.16,5 16.5,5.34 16.5,5.75V6.25Z" />
          </svg>
          Inventaire
        </button>
      </div>
    </div>
    <div id="tableContainer">
      @include('stock.partials.table', ['stocks' => $stocks])
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
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    const inventaireBtn = document.getElementById('inventaireBtn');

    // Attacher les événements d'exportation
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            console.log('Bouton Excel stocks cliqué');
            exportStocksExcel();
        });
    }

    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function() {
            console.log('Bouton PDF stocks cliqué');
            exportStocksPdf();
        });
    }

    if (inventaireBtn) {
        inventaireBtn.addEventListener('click', function() {
            console.log('Bouton Inventaire cliqué');
            exportInventaire();
        });
    }

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
            
            // Réattacher les événements de pagination
            attachPaginationEvents();
            
            // Masquer le spinner
            searchSpinner.classList.add('hidden');
            
            // Mettre à jour l'URL sans recharger la page
            window.history.pushState({}, '', url.toString());
        })
        .catch(error => {
            console.error('Erreur lors de la recherche stocks:', error);
            searchSpinner.classList.add('hidden');
        });
    }

    // Événement de saisie dans le champ de recherche
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            
            // Annuler la recherche précédente
            clearTimeout(searchTimeout);
            
            // Lancer une nouvelle recherche après 300ms
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });
    }

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
                    attachPaginationEvents();
                    searchSpinner.classList.add('hidden');
                    window.history.pushState({}, '', url.toString());
                })
                .catch(error => {
                    console.error('Erreur lors de la pagination stocks:', error);
                    searchSpinner.classList.add('hidden');
                });
            });
        });
    }

    // Initialiser les événements de pagination au chargement
    attachPaginationEvents();
    const tableContainer = document.getElementById('tableContainer');
});

// Fonctions d'exportation des stocks
function exportStocksExcel() {
    try {
        console.log('Export Excel stocks appelé');
        
        // Récupérer la valeur actuelle du champ de recherche
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const searchParam = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
        
        const url = `{{ route('export.stocks.excel') }}${searchParam}`;
        console.log('URL Excel stocks:', url);
        
        window.location.href = url;
    } catch (error) {
        console.error('Erreur lors de l\'export Excel stocks:', error);
        alert('Erreur lors de l\'exportation Excel');
    }
}

function exportStocksPdf() {
    try {
        console.log('Export PDF stocks appelé');
        
        // Récupérer la valeur actuelle du champ de recherche
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const searchParam = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
        
        const url = `{{ route('export.stocks.pdf') }}${searchParam}`;
        console.log('URL PDF stocks:', url);
        
        window.open(url, '_blank');
    } catch (error) {
        console.error('Erreur lors de l\'export PDF stocks:', error);
        alert('Erreur lors de l\'exportation PDF');
    }
}

// Fonction de confirmation de suppression de stock
function confirmDeleteStock(stockId, productName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le stock du produit "${productName}" ?\n\nCette action supprimera définitivement les données de stock.`)) {
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock/${stockId}`;
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Méthode DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonction d'export inventaire
function exportInventaire() {
    try {
        console.log('Export Inventaire appelé');
        
        // Récupérer la valeur actuelle du champ de recherche
        const searchInput = document.getElementById('searchInput');
        const searchValue = searchInput ? searchInput.value.trim() : '';
        const searchParam = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
        
        const url = `{{ route('export.stocks.inventaire') }}${searchParam}`;
        console.log('URL Inventaire:', url);
        
        window.open(url, '_blank');
    } catch (error) {
        console.error('Erreur lors de l\'export inventaire:', error);
        alert('Erreur lors de l\'exportation inventaire');
    }
}
</script>
@endsection
