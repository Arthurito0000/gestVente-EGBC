@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Devis</h1>
            <p class="text-gray-600 mt-1">Créez et gérez vos devis estimatifs</p>
        </div>
        @can('create-sales')
        <a href="{{ route('quotes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouveau Devis
        </a>
        @endcan
    </div>

    <!-- Recherche -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex items-center gap-4">
            <!-- Recherche -->
            <div class="relative flex-1">
                <input type="text" 
                    id="searchInput" 
                    placeholder="Rechercher (N° devis, client, objet...)" 
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    value="{{ request('search') }}">
                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <div id="searchSpinner" class="hidden absolute right-3 top-3">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Total -->
            <div class="text-center px-4">
                <div class="font-bold text-lg text-blue-600">{{ $quotes->total() }}</div>
                <div class="text-gray-600 text-sm">Total</div>
            </div>
        </div>
    </div>

    <!-- Table des devis -->
    <div id="quotesTable">
        @include('quotes.partials.table', ['quotes' => $quotes])
    </div>
</div>

@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const quotesTable = document.getElementById('quotesTable');

    function performSearch() {
        const search = searchInput.value;

        searchSpinner.classList.remove('hidden');

        fetch(`{{ route('quotes.index') }}?search=${encodeURIComponent(search)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            quotesTable.innerHTML = html;
            searchSpinner.classList.add('hidden');
            
            // Mettre à jour l'URL sans recharger la page
            const url = new URL(window.location);
            url.searchParams.set('search', search);
            window.history.pushState({}, '', url);

            // Réattacher les événements de pagination
            attachPaginationEvents();
        })
        .catch(error => {
            console.error('Erreur:', error);
            searchSpinner.classList.add('hidden');
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });

    function attachPaginationEvents() {
        document.querySelectorAll('#quotesTable .pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    quotesTable.innerHTML = html;
                    window.history.pushState({}, '', url);
                    attachPaginationEvents();
                });
            });
        });
    }

    attachPaginationEvents();
</script>
@endpush
@endsection
