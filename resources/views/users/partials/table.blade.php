<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Utilisateur
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rôle
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Créé le
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-white font-medium text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $user->name }}
                            </div>
                            @if($user->id === auth()->id())
                                <div class="text-xs text-blue-600 font-medium">(Vous)</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->roles->isNotEmpty())
                        @php
                            $role = $user->roles->first();
                            $roleColors = [
                                'administrateur' => 'bg-red-100 text-red-800',
                                'gerant_stock' => 'bg-green-100 text-green-800',
                                'vendeur' => 'bg-blue-100 text-blue-800',
                            ];
                            $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                            {{ $user->getFormattedRoleName() }}
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Aucun rôle
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {!! $user->getStatutBadge() !!}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-1">
                        <!-- Voir -->
                        <a href="{{ route('users.show', $user) }}" 
                           class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 hover:scale-110"
                           title="Voir les détails">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>

                        @can('manage-users')
                        <!-- Modifier -->
                        <a href="{{ route('users.edit', $user) }}" 
                           class="p-2.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all duration-200 hover:scale-110"
                           title="Modifier l'utilisateur">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>

                        <!-- Activer/Désactiver -->
                        @if($user->id !== auth()->id())
                        <button onclick="confirmStatusToggle('{{ $user->name }}', '{{ route('users.toggle-status', $user) }}', '{{ $user->statut }}', {{ $user->id }})" 
                                class="p-2.5 text-gray-400 hover:text-{{ $user->statut === 'actif' ? 'orange' : 'green' }}-600 hover:bg-{{ $user->statut === 'actif' ? 'orange' : 'green' }}-50 rounded-xl transition-all duration-200 hover:scale-110"
                                title="{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }} l'utilisateur">
                            @if($user->statut === 'actif')
                                <!-- Icône désactiver -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                            @else
                                <!-- Icône activer -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </button>
                        @endif

                        <!-- Supprimer -->
                        @if($user->id !== auth()->id())
                        <button onclick="confirmDelete('{{ $user->name }}', '{{ route('users.destroy', $user) }}')" 
                                class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 hover:scale-110"
                                title="Supprimer l'utilisateur">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        @endif
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Aucun utilisateur trouvé</p>
                        <p class="text-gray-400 text-sm mt-1">
                            @if(request('search'))
                                Aucun résultat pour "{{ request('search') }}"
                            @else
                                Commencez par créer votre premier utilisateur
                            @endif
                        </p>
                        @can('manage-users')
                        <a href="{{ route('users.create') }}" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Créer un utilisateur
                        </a>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div class="px-6 py-3 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats
        </div>
        <div class="pagination">
            {{ $users->appends(request()->query())->links('vendor.pagination.simple-tailwind') }}
        </div>
    </div>
</div>
@endif

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60  overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border-none w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-sm text-gray-500 mb-4">
                ⚠️ Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?
            </p>
            <p class="text-xs text-red-600 mb-6">Cette action est irréversible.</p>
            <div class="flex gap-3 justify-center">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <span id="deleteButtonText">Supprimer</span>
                        <svg id="deleteSpinner" class="animate-spin h-4 w-4 text-white hidden inline ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de changement de statut -->
<div id="statusModal" class="fixed shadow-lg inset-0 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60  overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border-none w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="statusIconContainer" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                <svg id="statusIcon" class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2" id="statusModalTitle">Confirmer le changement</h3>
            <p class="text-sm text-gray-500 mb-4" id="statusModalMessage">
                <!-- Message dynamique -->
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <p class="text-xs text-yellow-800">
                    <strong>⚠️ Important :</strong> <span id="statusWarningMessage"><!-- Message d'avertissement dynamique --></span>
                </p>
            </div>
            <div class="flex gap-3 justify-center">
                <button id="cancelStatus" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Annuler
                </button>
                <form id="statusForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" id="confirmStatus" class="px-4 py-2 rounded-lg transition-colors">
                        <span id="statusButtonText">Confirmer</span>
                        <svg id="statusSpinner" class="animate-spin h-4 w-4 text-white hidden inline ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userName, deleteUrl) {
    const modal = document.getElementById('deleteModal');
    const userNameSpan = document.getElementById('deleteUserName');
    const deleteForm = document.getElementById('deleteForm');
    
    userNameSpan.textContent = userName;
    deleteForm.action = deleteUrl;
    modal.classList.remove('hidden');
}

function confirmStatusToggle(userName, statusUrl, currentStatus, userId) {
    const modal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    const statusModalTitle = document.getElementById('statusModalTitle');
    const statusModalMessage = document.getElementById('statusModalMessage');
    const statusWarningMessage = document.getElementById('statusWarningMessage');
    const statusButtonText = document.getElementById('statusButtonText');
    const confirmButton = document.getElementById('confirmStatus');
    const statusIconContainer = document.getElementById('statusIconContainer');
    const statusIcon = document.getElementById('statusIcon');
    
    const isDeactivating = currentStatus === 'actif';
    
    // Configuration du modal selon l'action
    if (isDeactivating) {
        statusModalTitle.textContent = 'Désactiver l\'utilisateur';
        statusModalMessage.innerHTML = `Êtes-vous sûr de vouloir <strong>désactiver</strong> l'utilisateur <strong>${userName}</strong> ?`;
        statusWarningMessage.textContent = 'L\'utilisateur sera immédiatement déconnecté et ne pourra plus accéder au système.';
        statusButtonText.textContent = 'Désactiver';
        confirmButton.className = 'px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors';
        statusIconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 mb-4';
        statusIcon.className = 'h-6 w-6 text-orange-600 animate-pulse';
        statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>';
    } else {
        statusModalTitle.textContent = 'Activer l\'utilisateur';
        statusModalMessage.innerHTML = `Êtes-vous sûr de vouloir <strong>réactiver</strong> l'utilisateur <strong>${userName}</strong> ?`;
        statusWarningMessage.textContent = 'L\'utilisateur pourra à nouveau se connecter et accéder au système selon ses permissions.';
        statusButtonText.textContent = 'Activer';
        confirmButton.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
        statusIconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
        statusIcon.className = 'h-6 w-6 text-green-600 animate-pulse';
        statusIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    }
    
    statusForm.action = statusUrl;
    modal.classList.remove('hidden');
}

// Événements pour fermer les modaux
document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').classList.add('hidden');
});

document.getElementById('cancelStatus').addEventListener('click', function() {
    document.getElementById('statusModal').classList.add('hidden');
});

// Fermer les modaux en cliquant à l'extérieur
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('statusModal').classList.add('hidden');
    }
});
</script>
