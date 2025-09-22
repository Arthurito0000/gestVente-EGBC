@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <!-- KPIs -->
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
    <div class="bg-white rounded-2xl shadow-md p-5">
      <div class="text-sm text-gray-500">Produits</div>
      <div class="mt-2 flex items-end justify-between">
        <div class="text-2xl font-heading text-gray-900">342</div>
        <span class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-700">+4%</span>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-md p-5">
      <div class="text-sm text-gray-500">Stock Total</div>
      <div class="mt-2 flex items-end justify-between">
        <div class="text-2xl font-heading text-gray-900">12,480</div>
        <span class="text-xs px-2 py-1 rounded bg-primary-100 text-primary-700">+1.2%</span>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-md p-5">
      <div class="text-sm text-gray-500">Entrées (30j)</div>
      <div class="mt-2 flex items-end justify-between">
        <div class="text-2xl font-heading text-gray-900">1,024</div>
        <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">+12%</span>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-md p-5">
      <div class="text-sm text-gray-500">Sorties (30j)</div>
      <div class="mt-2 flex items-end justify-between">
        <div class="text-2xl font-heading text-gray-900">987</div>
        <span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">-2%</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Recent Movements -->
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-md">
      <div class="p-5 border-b flex items-center justify-between">
        <h3 class="font-heading text-lg text-gray-900">Mouvements récents</h3>
        <a href="{{ route('movements.index') }}" class="text-sm text-primary-700 hover:text-primary-600">Voir tout</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @foreach([
              ['date'=>'2025-09-20 10:24','produit'=>'SKU-001 • Souris','type'=>'ENTREE','qte'=>120],
              ['date'=>'2025-09-20 09:13','produit'=>'SKU-114 • Clavier','type'=>'SORTIE','qte'=>-15],
              ['date'=>'2025-09-19 18:02','produit'=>'SKU-221 • Écran 24"','type'=>'ENTREE','qte'=>30],
            ] as $row)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm text-gray-700">{{ $row['date'] }}</td>
              <td class="px-4 py-2 text-sm text-gray-900">{{ $row['produit'] }}</td>
              <td class="px-4 py-2 text-sm">
                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $row['type']==='ENTREE' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $row['type'] }}</span>
              </td>
              <td class="px-4 py-2 text-sm text-right font-medium {{ $row['qte']>0 ? 'text-green-700' : 'text-red-700' }}">{{ $row['qte']>0?'+':'' }}{{ $row['qte'] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Low stock -->
    <div class="bg-white rounded-2xl shadow-md">
      <div class="p-5 border-b">
        <h3 class="font-heading text-lg text-gray-900">Ruptures potentielles</h3>
      </div>
      <ul class="p-3 space-y-2 max-h-80 overflow-y-auto">
        @foreach([
          ['sku'=>'SKU-778','nom'=>'Câble HDMI','stock'=>3],
          ['sku'=>'SKU-311','nom'=>'Batterie 18650','stock'=>5],
          ['sku'=>'SKU-992','nom'=>'Adaptateur USB-C','stock'=>7],
        ] as $p)
        <li class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50">
          <div>
            <div class="text-sm font-medium text-gray-900">{{ $p['sku'] }} — {{ $p['nom'] }}</div>
            <div class="text-xs text-gray-500">Stock actuel</div>
          </div>
          <div class="text-sm font-semibold text-red-600">{{ $p['stock'] }}</div>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endsection
