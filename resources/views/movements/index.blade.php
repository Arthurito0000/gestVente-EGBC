@extends('layouts.app')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="font-heading text-2xl text-gray-900">Mouvements</h1>
      <p class="text-gray-500">Entrées et sorties de stock (ENTREE / SORTIE).</p>
    </div>
    <div class="flex items-center gap-3">
      <button id="refreshBtn" class="px-4 py-2 rounded-lg border">Actualiser</button>
      <div class="relative">
        <a href="{{ route('movements.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouveau mouvement</a>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-4 border-b flex items-center justify-between">
      <div class="text-sm text-gray-600">Historique des mouvements</div>
      <div id="lastUpdated" class="text-xs text-gray-500">Mis à jour: maintenant</div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200" id="movementsTable">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantité</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @forelse($movements as $movement)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-700">{{ $movement->date->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-2 text-sm text-gray-900">{{ $movement->product->sku }} • {{ $movement->product->nom }}</td>
            <td class="px-4 py-2 text-sm text-gray-600">{{ $movement->motif }}</td>
            <td class="px-4 py-2 text-sm text-center">
              <span class="px-2 py-0.5 rounded text-xs font-medium {{ $movement->type === 'ENTREE' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $movement->type }}</span>
            </td>
            <td class="px-4 py-2 text-sm text-right font-medium {{ $movement->type === 'ENTREE' ? 'text-green-700' : 'text-red-700' }}">
              {{ $movement->type === 'ENTREE' ? '+' : '-' }}{{ $movement->quantite }}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              Aucun mouvement trouvé. <a href="{{ route('products.create') }}" class="text-primary-700 hover:text-primary-600">Créer le premier produit</a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    @if($movements->hasPages())
      <div class="px-6 py-4 border-t border-gray-200">
        {{ $movements->links() }}
      </div>
    @endif
  </div>
</div>

<script>
// Dummy AJAX refresh example
const btn = document.getElementById('refreshBtn');
const updated = document.getElementById('lastUpdated');
btn?.addEventListener('click', async () => {
  btn.disabled = true; btn.textContent = 'Actualisation…';
  // Simulate async fetch
  await new Promise(r => setTimeout(r, 600));
  const ts = new Date();
  updated.textContent = 'Mis à jour: ' + ts.toLocaleTimeString();
  btn.disabled = false; btn.textContent = 'Actualiser';
});
</script>
@endsection
