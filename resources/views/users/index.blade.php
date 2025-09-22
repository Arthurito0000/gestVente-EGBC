@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Utilisateurs</h1>
      <p class="text-gray-500">Gérez les comptes utilisateurs et leurs rôles.</p>
    </div>
    <div class="flex items-center gap-3">
      <input type="text" placeholder="Rechercher…" class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
      <a href="#" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouvel utilisateur</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des utilisateurs</div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach([
            ['username'=>'admin','role'=>'Admin','statut'=>'Actif'],
            ['username'=>'vendeur1','role'=>'Vendeur','statut'=>'Actif'],
            ['username'=>'audit','role'=>'Lecture seule','statut'=>'Inactif'],
          ] as $u)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-900">{{ $u['username'] }}</td>
            <td class="px-4 py-2 text-sm"><span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $u['role'] }}</span></td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $u['statut'] }}</td>
            <td class="px-4 py-2 text-sm text-right"><a href="#" class="text-primary-700 hover:text-primary-600">Gérer</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
