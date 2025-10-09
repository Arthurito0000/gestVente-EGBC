@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Permissions</h1>
      <p class="text-gray-500">Liste et gestion des permissions système.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="#" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouvelle permission</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Permissions par catégorie</div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permission</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach([
            ['name'=>'view-dashboard','category'=>'Dashboard'],
            ['name'=>'manage-products','category'=>'Produits'],
            ['name'=>'view-stock','category'=>'Stock'],
            ['name'=>'manage-movements','category'=>'Mouvements'],
            ['name'=>'manage-invoices','category'=>'Factures'],
            ['name'=>'manage-users','category'=>'Utilisateurs'],
          ] as $perm)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm font-mono text-gray-900">{{ $perm['name'] }}</td>
            <td class="px-4 py-2 text-sm"><span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $perm['category'] }}</span></td>
            <td class="px-4 py-2 text-sm text-right"><a class="text-primary-700 hover:text-primary-600" href="#">Éditer</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
