@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Produits</h1>
      <p class="text-gray-500">Gérez le catalogue produits (SKU, prix, etc.).</p>
    </div>
    <div class="flex items-center gap-3">
      <input type="text" placeholder="Rechercher…" class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
      <a href="{{ route('products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouveau produit</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des produits</div>
      <div class="flex items-center gap-2 text-sm">
        <button class="px-3 py-1 rounded border">Exporter</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix Achat</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix Vente</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach([
            ['sku'=>'SKU-001','nom'=>'Souris Optique','pa'=>5.50,'pv'=>9.90],
            ['sku'=>'SKU-114','nom'=>'Clavier Mécanique','pa'=>32.00,'pv'=>59.00],
            ['sku'=>'SKU-221','nom'=>'Écran 24"','pa'=>110.00,'pv'=>149.00],
          ] as $p)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $p['sku'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-900">{{ $p['nom'] }}</td>
            <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($p['pa'],2,',',' ') }} €</td>
            <td class="px-4 py-2 text-sm text-right text-gray-700">{{ number_format($p['pv'],2,',',' ') }} €</td>
            <td class="px-4 py-2 text-sm text-right">
              <a href="#" class="text-primary-700 hover:text-primary-600">Détails</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
