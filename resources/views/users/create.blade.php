@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Créer un Utilisateur</h1>
            <p class="text-gray-500">Ajoutez un nouvel utilisateur au système avec son rôle.</p>
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
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500 @error('name') border-red-500 @enderror"
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
                        value="{{ old('email') }}"
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500 @error('email') border-red-500 @enderror"
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
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500 @error('role') border-red-500 @enderror"
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
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
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
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Minimum 8 caractères"
                        required
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmer le mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full rounded-lg border-gray-300 focus:outline-none focus:ring-0 focus:border-blue-500"
                        placeholder="Répétez le mot de passe"
                        required
                    >
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation côté client
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirmation = document.getElementById('password_confirmation').value;
    
    if (password !== passwordConfirmation) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas !');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 8 caractères !');
        return false;
    }
});
</script>
@endsection
