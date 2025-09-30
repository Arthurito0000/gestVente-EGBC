@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Gestion des Utilisateurs</h1>
            <p class="text-gray-500">Gérez les comptes utilisateurs et leurs rôles dans le système.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Rechercher par nom ou email..." 
                    value="{{ $search }}"
                    class="w-80 rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500" 
                />
            </div>
            <div id="searchSpinner" class="hidden">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            @can('manage-users')
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nouvel utilisateur
            </a>
            @endcan
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Liste des utilisateurs ({{ $users->total() }} au total)
            </div>
            @can('manage-roles')
            <a href="{{ route('users.roles') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Gérer les rôles et permissions
            </a>
            @endcan
        </div>
        
        <div id="usersTable">
            @include('users.partials.table')
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const usersTable = document.getElementById('usersTable');
    let searchTimeout;

    // Fonction de recherche AJAX
    function performSearch() {
        const searchTerm = searchInput.value;
        
        searchSpinner.classList.remove('hidden');
        
        fetch(`{{ route('users.index') }}?search=${encodeURIComponent(searchTerm)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(html => {
            usersTable.innerHTML = html;
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
        const paginationLinks = document.querySelectorAll('#usersTable .pagination-links a, #usersTable .pagination a');
        
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
                    usersTable.innerHTML = html;
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
