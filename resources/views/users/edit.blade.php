@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Modifier l'Utilisateur</h1>
            <p class="text-gray-500">Modifiez les informations de {{ $user->name }}.</p>
        </div>
        <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Ex: Jean Dupont"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="Ex: jean.dupont@gesteventes.com"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rôle -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Rôle <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="role" 
                        name="role" 
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror"
                        required
                    >
                        <option value="">Sélectionnez un rôle</option>
                        @foreach($roles as $role)
                            @php
                                $roleLabels = [
                                    'administrateur' => 'Administrateur',
                                    'gerant_stock' => 'Gérant de Stock',
                                    'vendeur' => 'Vendeur',
                                ];
                                $roleDescriptions = [
                                    'administrateur' => 'Accès complet au système',
                                    'gerant_stock' => 'Gestion des stocks et produits',
                                    'vendeur' => 'Gestion des ventes uniquement',
                                ];
                            @endphp
                            <option value="{{ $role->name }}" {{ old('role', $userRole) == $role->name ? 'selected' : '' }}>
                                {{ $roleLabels[$role->name] ?? $role->name }} - {{ $roleDescriptions[$role->name] ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau mot de passe <span class="text-gray-400">(optionnel)</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Laissez vide pour conserver le mot de passe actuel"
                    >
                    <p class="mt-1 text-sm text-gray-500">Minimum 8 caractères si vous souhaitez le changer</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Répétez le nouveau mot de passe"
                    >
                </div>

                <!-- Informations supplémentaires -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Informations du compte</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Créé le :</span> {{ $user->created_at->format('d/m/Y à H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Dernière modification :</span> {{ $user->updated_at->format('d/m/Y à H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Rôle actuel :</span> {{ $user->getFormattedRoleName() }}
                        </div>
                        <div>
                            <span class="font-medium">Permissions :</span> 
                            @if($user->roles->isNotEmpty())
                                {{ $user->roles->first()->permissions->count() }} permissions
                            @else
                                Aucune permission
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Annuler
                        </a>
                        <a href="{{ route('users.show', $user) }}" class="px-4 py-2 text-blue-600 hover:text-blue-700">
                            Voir les détails
                        </a>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Avertissement pour l'utilisateur connecté -->
    @if($user->id === auth()->id())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    ⚠️ Vous modifiez votre propre compte
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Soyez prudent lors de la modification de votre rôle ou mot de passe. Si vous perdez vos permissions d'administrateur, vous ne pourrez plus accéder à cette section.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Validation côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    // Si un mot de passe est saisi, vérifier la confirmation
    if (password && password !== passwordConfirmation) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas !');
        return false;
    }
    
    // Si un mot de passe est saisi, vérifier la longueur
    if (password && password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères !');
        return false;
    }
});

// Avertissement pour changement de rôle de son propre compte
@if($user->id === auth()->id())
document.getElementById('role').addEventListener('change', function() {
    const currentRole = '{{ $userRole }}';
    const newRole = this.value;
    
    if (currentRole === 'administrateur' && newRole !== 'administrateur') {
        if (!confirm('⚠️ ATTENTION !\n\nVous êtes sur le point de retirer vos privilèges d\'administrateur. Vous perdrez l\'accès à cette section de gestion des utilisateurs.\n\nÊtes-vous sûr de vouloir continuer ?')) {
            this.value = currentRole;
        }
    }
});
@endif
</script>
@endsection
