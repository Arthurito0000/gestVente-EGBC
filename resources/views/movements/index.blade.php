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
        <button class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouveau mouvement</button>
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
          @foreach([
            ['date'=>'2025-09-20 10:24','produit'=>'SKU-001 • Souris','motif'=>'Réception fournisseur','type'=>'ENTREE','qte'=>120],
            ['date'=>'2025-09-20 09:13','produit'=>'SKU-114 • Clavier','motif'=>'Vente #INV-1023','type'=>'SORTIE','qte'=>-15],
            ['date'=>'2025-09-19 18:02','produit'=>'SKU-221 • Écran 24"','motif'=>'Réapprovisionnement','type'=>'ENTREE','qte'=>30],
          ] as $row)
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 text-sm text-gray-700">{{ $row['date'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-900">{{ $row['produit'] }}</td>
            <td class="px-4 py-2 text-sm text-gray-600">{{ $row['motif'] }}</td>
            <td class="px-4 py-2 text-sm text-center">
              <span class="px-2 py-0.5 rounded text-xs font-medium {{ $row['type']==='ENTREE' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $row['type'] }}</span>
            </td>
            <td class="px-4 py-2 text-sm text-right font-medium {{ $row['qte']>0 ? 'text-green-700' : 'text-red-700' }}">{{ $row['qte']>0?'+':'' }}{{ $row['qte'] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
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
