@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Stock</h1>
      <p class="text-gray-500">Suivi des niveaux de stock par produit.</p>
    </div>
    <div class="flex items-center gap-3">
      <input type="text" placeholder="Rechercher…" class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
      <div class="flex gap-2">
        <button class="px-4 py-2 rounded-lg border">Exporter</button>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Niveaux actuels</div>
      <div class="text-sm">Dernière mise à jour: <span class="font-medium text-gray-900">il y a 5 min</span></div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qte Stock</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Seuil</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @forelse($stocks as $stock)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-900">{{ $stock->product->nom }}</td>
            <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $stock->product->sku }}</td>
            <td class="px-4 py-2 text-sm text-right font-medium {{ $stock->quantite < $stock->seuil ? 'text-red-600' : 'text-gray-900' }}">{{ $stock->quantite }}</td>
            <td class="px-4 py-2 text-sm text-right text-gray-700">{{ $stock->seuil }}</td>
           
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              Aucun stock trouvé. <a href="{{ route('products.create') }}" class="text-primary-700 hover:text-primary-600">Créer le premier produit</a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    @if($stocks->hasPages())
      <div class="px-6 py-4 border-t border-gray-200">
        {{ $stocks->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
