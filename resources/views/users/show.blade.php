@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Détails de l'Utilisateur</h1>
            <p class="text-gray-500">Informations complètes et permissions de {{ $user->name }}.</p>
        </div>
        <div class="flex items-center gap-3">
            @can('manage-users')
            <a href="{{ route('users.edit', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Informations Personnelles</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 h-20 w-20">
                            <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            @if($user->id === auth()->id())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                                    C'est vous
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <p class="text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Membre depuis</label>
                            <p class="text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dernière modification</label>
                            <p class="text-gray-900">{{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rôle et statut -->
        <div class="space-y-6">
            <!-- Rôle -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Rôle</h2>
                </div>
                <div class="p-6">
                    @if($user->roles->isNotEmpty())
                        @php
                            $role = $user->roles->first();
                            $roleColors = [
                                'administrateur' => 'bg-red-100 text-red-800 border-red-200',
                                'gerant_stock' => 'bg-green-100 text-green-800 border-green-200',
                                'vendeur' => 'bg-blue-100 text-blue-800 border-blue-200',
                            ];
                            $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <div class="text-center">
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border {{ $colorClass }}">
                                {{ $user->getFormattedRoleName() }}
                            </div>
                            <p class="text-gray-600 text-sm mt-2">
                                @switch($role->name)
                                    @case('administrateur')
                                        Accès complet au système
                                        @break
                                    @case('gerant_stock')
                                        Gestion des stocks et produits
                                        @break
                                    @case('vendeur')
                                        Gestion des ventes uniquement
                                        @break
                                    @default
                                        Rôle personnalisé
                                @endswitch
                            </p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                Aucun rôle assigné
                            </div>
                            <p class="text-gray-600 text-sm mt-2">Cet utilisateur n'a aucun rôle</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Statistiques</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Permissions</span>
                            <span class="font-semibold">
                                @if($user->roles->isNotEmpty())
                                    {{ $user->roles->first()->permissions->count() }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Compte créé il y a</span>
                            <span class="font-semibold">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dernière activité</span>
                            <span class="font-semibold">{{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions détaillées -->
    @if($user->roles->isNotEmpty() && $user->roles->first()->permissions->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Permissions Détaillées</h2>
            <p class="text-gray-600 text-sm">Liste complète des permissions accordées à cet utilisateur</p>
        </div>
        <div class="p-6">
            @php
                $permissions = $user->roles->first()->permissions->groupBy(function ($permission) {
                    return explode('-', $permission->name)[0];
                });
                
                $sectionLabels = [
                    'view' => 'Consultation',
                    'create' => 'Création',
                    'edit' => 'Modification',
                    'delete' => 'Suppression',
                    'manage' => 'Gestion',
                    'export' => 'Exportation',
                    'receive' => 'Notifications',
                ];
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($permissions as $section => $perms)
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3 capitalize">
                        {{ $sectionLabels[$section] ?? ucfirst($section) }}
                    </h3>
                    <div class="space-y-2">
                        @foreach($perms as $permission)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">
                                {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
