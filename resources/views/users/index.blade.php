@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Gestion des Utilisateurs</h1>
            <p class="text-gray-500">Gérez les comptes utilisateurs et leurs rôles dans le système.</p>
            </div>
            <div class="flex gap-3">
                @can('manage-users')
                    <a href="{{ route('users.emergency-reset') }}" 
                       class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Reset d'urgence
                    </a>
                @endcan
                @can('manage-roles')
                    <a href="{{ route('users.roles') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Gérer les rôles
                    </a>
                @endcan
                @can('manage-users')
                    <a href="{{ route('users.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
