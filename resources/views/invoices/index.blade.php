@extends('layouts.app')


@if (session('pdf_download'))
    <script>
        window.onload = function () {
            const link = document.createElement('a');
            link.href = "{{ session('pdf_download') }}";
            link.download = '';
            link.target = '_blank';
            link.click();
        };
    </script>
@endif


@section('content')
    <div class="space-y-6 w-full px-4 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl text-gray-900">Factures</h1>
                <p class="text-gray-500">Gestion des factures et lignes de facturation.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Rechercher par client ou code..." 
                        value="{{ $search ?? '' }}"
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500 px-4 py-2" 
                    />
                </div>
                <div id="searchSpinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <a href="{{ route('invoices.create') }}"
                    class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2 w-full sm:w-auto text-center">Nouvelle facture</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-sm text-gray-600">Liste des factures</div>
                <div class="flex items-center gap-2 text-sm">
                    <button class="px-3 py-1 rounded border">Exporter</button>
                </div>
            </div>
            <div id="invoicesTable">
                @include('invoices.partials.table')
            </div>
        </div>
    </div>

<style>
/* Styles pour la pagination bleue */
.pagination-links .relative {
  @apply inline-flex items-center;
}

.pagination-links a, .pagination-links span {
  @apply px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-blue-50 hover:text-blue-600;
}

.pagination-links .bg-blue-50 {
  @apply bg-blue-600 text-white border-blue-600;
}

.pagination-links a:first-child, .pagination-links span:first-child {
  @apply rounded-l-lg;
}

.pagination-links a:last-child, .pagination-links span:last-child {
  @apply rounded-r-lg;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .pagination-links a, .pagination-links span {
    @apply px-2 py-1 text-xs;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const invoicesTable = document.getElementById('invoicesTable');
    let searchTimeout;

    // Fonction de recherche AJAX
    function performSearch() {
        const searchTerm = searchInput.value;
        
        searchSpinner.classList.remove('hidden');
        
        fetch(`{{ route('invoices.index') }}?search=${encodeURIComponent(searchTerm)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(html => {
            invoicesTable.innerHTML = html;
            searchSpinner.classList.add('hidden');
            
            // Réattacher les événements pour la pagination
            attachPaginationEvents();
        })
        .catch(error => {
            console.error('Erreur lors de la recherche:', error);
            searchSpinner.classList.add('hidden');
        });
    }

    // Recherche avec délai
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });

    // Fonction pour attacher les événements de pagination
    function attachPaginationEvents() {
        const paginationLinks = document.querySelectorAll('#invoicesTable .pagination-links a, #invoicesTable .pagination a');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = new URL(this.href);
                const searchTerm = searchInput.value;
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                }
                
                searchSpinner.classList.remove('hidden');
                
                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.text())
                .then(html => {
                    invoicesTable.innerHTML = html;
                    searchSpinner.classList.add('hidden');
                    attachPaginationEvents();
                })
                .catch(error => {
                    console.error('Erreur lors du changement de page:', error);
                    searchSpinner.classList.add('hidden');
                });
            });
        });
    }

    // Attacher les événements initiaux
    attachPaginationEvents();
});
</script>
@endsection