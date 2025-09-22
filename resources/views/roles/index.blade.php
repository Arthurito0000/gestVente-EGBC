@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Rôles</h1>
      <p class="text-gray-500">Définissez les rôles et leurs permissions.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="#" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouveau rôle</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des rôles</div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Utilisateurs</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach([
            ['name'=>'Admin','users'=>2,'perms'=>'Tous'],
            ['name'=>'Vendeur','users'=>5,'perms'=>'Produits, Stock, Ventes'],
            ['name'=>'Lecture seule','users'=>1,'perms'=>'Vue'],
          ] as $r)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-900">{{ $r['name'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $r['users'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $r['perms'] }}</td>
            <td class="px-4 py-2 text-sm text-right"><a class="text-primary-700 hover:text-primary-600" href="#">Gérer</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
