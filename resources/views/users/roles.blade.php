@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Gestion des Rôles et Permissions</h1>
            <p class="text-gray-500">Configurez les permissions pour chaque rôle du système.</p>
        </div>
        <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour aux utilisateurs
        </a>
    </div>

    <!-- Vue d'ensemble des rôles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($roles as $role)
            @php
                $roleInfo = [
                    'administrateur' => [
                        'name' => 'Administrateur',
                        'description' => 'Accès complet au système',
                        'color' => 'red',
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
                    ],
                    'gerant_stock' => [
                        'name' => 'Gérant de Stock',
                        'description' => 'Gestion des stocks et produits',
                        'color' => 'green',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
                    ],
                    'vendeur' => [
                        'name' => 'Vendeur',
                        'description' => 'Gestion des ventes uniquement',
                        'color' => 'blue',
                        'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
                    ]
                ];
                $info = $roleInfo[$role->name] ?? [
                    'name' => ucfirst($role->name),
                    'description' => 'Rôle personnalisé',
                    'color' => 'gray',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
                ];
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12 bg-{{ $info['color'] }}-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-{{ $info['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $info['name'] }}</h3>
                            <p class="text-gray-600 text-sm">{{ $info['description'] }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Permissions</span>
                            <span class="text-sm font-semibold text-{{ $info['color'] }}-600">
                                {{ $role->permissions->count() }}
                            </span>
                        </div>
                        
                        <button 
                            onclick="openRoleModal('{{ $role->name }}', '{{ $info['name'] }}')"
                            class="w-full bg-{{ $info['color'] }}-600 hover:bg-{{ $info['color'] }}-700 text-white px-4 py-2 rounded-lg text-sm transition-colors"
                        >
                            Gérer les permissions
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tableau détaillé des permissions par rôle -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Matrice des Permissions</h2>
            <p class="text-gray-600 text-sm">Vue d'ensemble des permissions accordées à chaque rôle</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permission
                        </th>
                        @foreach($roles as $role)
                            @php
                                $roleLabels = [
                                    'administrateur' => 'Admin',
                                    'gerant_stock' => 'Stock',
                                    'vendeur' => 'Vendeur',
                                ];
                            @endphp
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $roleLabels[$role->name] ?? $role->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($permissions as $section => $perms)
                        <tr class="bg-gray-50">
                            <td colspan="{{ count($roles) + 1 }}" class="px-6 py-3 text-sm font-medium text-gray-900 uppercase tracking-wider">
                                {{ ucfirst($section) }}
                            </td>
                        </tr>
                        @foreach($perms as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                            </td>
                            @foreach($roles as $role)
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($role->hasPermissionTo($permission->name))
                                        <svg class="w-5 h-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de gestion des permissions -->
<div id="permissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Gérer les permissions</h3>
                <p class="text-gray-600 text-sm" id="modalSubtitle">Sélectionnez les permissions pour ce rôle</p>
            </div>
            <button onclick="closePermissionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="permissionForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($permissions as $section => $perms)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3 capitalize flex items-center">
                        <input 
                            type="checkbox" 
                            id="select-all-{{ $section }}" 
                            class="mr-2 section-select-all"
                            data-section="{{ $section }}"
                            onchange="toggleSectionPermissions('{{ $section }}')"
                        >
                        {{ ucfirst($section) }}
                    </h4>
                    <div class="space-y-2">
                        @foreach($perms as $permission)
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $permission->name }}"
                                class="mr-2 permission-checkbox section-{{ $section }}"
                                onchange="updateSectionCheckbox('{{ $section }}')"
                            >
                            <span class="text-sm text-gray-700">
                                {{ str_replace('-', ' ', ucwords(str_replace($section.'-', '', $permission->name))) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closePermissionModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Enregistrer les permissions
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentRole = null;

function openRoleModal(roleName, roleDisplayName) {
    currentRole = roleName;
    document.getElementById('modalTitle').textContent = `Permissions - ${roleDisplayName}`;
    document.getElementById('modalSubtitle').textContent = `Configurez les permissions pour le rôle ${roleDisplayName}`;
    
    // Mettre à jour l'action du formulaire
    document.getElementById('permissionForm').action = `/users/roles/${roleName}/permissions`;
    
    // Charger les permissions actuelles du rôle
    loadRolePermissions(roleName);
    
    document.getElementById('permissionModal').classList.remove('hidden');
}

function closePermissionModal() {
    document.getElementById('permissionModal').classList.add('hidden');
    currentRole = null;
}

function loadRolePermissions(roleName) {
    // Décocher toutes les cases d'abord
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Récupérer les permissions du rôle via AJAX
    fetch(`/users/roles/${roleName}/permissions`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.permissions) {
            data.permissions.forEach(permission => {
                const checkbox = document.querySelector(`input[value="${permission}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            
            // Mettre à jour les cases "Sélectionner tout"
            updateAllSectionCheckboxes();
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des permissions:', error);
    });
}

function toggleSectionPermissions(section) {
    const sectionCheckbox = document.getElementById(`select-all-${section}`);
    const permissionCheckboxes = document.querySelectorAll(`.section-${section}`);
    
    permissionCheckboxes.forEach(checkbox => {
        checkbox.checked = sectionCheckbox.checked;
    });
}

function updateSectionCheckbox(section) {
    const sectionCheckbox = document.getElementById(`select-all-${section}`);
    const permissionCheckboxes = document.querySelectorAll(`.section-${section}`);
    const checkedBoxes = document.querySelectorAll(`.section-${section}:checked`);
    
    if (checkedBoxes.length === 0) {
        sectionCheckbox.checked = false;
        sectionCheckbox.indeterminate = false;
    } else if (checkedBoxes.length === permissionCheckboxes.length) {
        sectionCheckbox.checked = true;
        sectionCheckbox.indeterminate = false;
    } else {
        sectionCheckbox.checked = false;
        sectionCheckbox.indeterminate = true;
    }
}

function updateAllSectionCheckboxes() {
    document.querySelectorAll('.section-select-all').forEach(sectionCheckbox => {
        const section = sectionCheckbox.dataset.section;
        updateSectionCheckbox(section);
    });
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('permissionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePermissionModal();
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('permissionModal').classList.contains('hidden')) {
        closePermissionModal();
    }
});

// Gérer la soumission du formulaire
document.getElementById('permissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher un message de succès (vous pouvez utiliser Toastr ici)
            alert('Permissions mises à jour avec succès !');
            closePermissionModal();
            // Recharger la page pour voir les changements
            window.location.reload();
        } else {
            alert('Erreur lors de la mise à jour des permissions.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour des permissions.');
    });
});
</script>
@endsection
