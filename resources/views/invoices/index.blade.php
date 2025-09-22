@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Factures</h1>
      <p class="text-gray-500">Gestion des factures et lignes de facturation.</p>
    </div>
    <div class="flex items-center gap-3">
      <input type="text" placeholder="Rechercher…" class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
      <a href="#" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouvelle facture</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Liste des factures</div>
      <div class="flex items-center gap-2 text-sm">
        <button class="px-3 py-1 rounded border">Exporter</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach([
            ['num'=>'INV-1023','date'=>'2025-09-20','total'=>1299.90],
            ['num'=>'INV-1022','date'=>'2025-09-18','total'=>249.00],
            ['num'=>'INV-1021','date'=>'2025-09-16','total'=>78.50],
          ] as $f)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $f['num'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-700">{{ $f['date'] }}</td>
            <td class="px-4 py-2 text-sm text-right text-gray-900">{{ number_format($f['total'],2,',',' ') }} €</td>
            <td class="px-4 py-2 text-sm text-right"><a class="text-primary-700 hover:text-primary-600" href="#">Détails</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
