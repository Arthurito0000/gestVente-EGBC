<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
            <th class="px-4 py-2"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($invoices as $f)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $f['code'] }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $f['invoice_date'] }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $f['client_name'] }}</td>
                <td class="px-4 py-2 text-sm italic text-gray-700">{{ $f['total_amount'] }} FCFA</td>
                <td class="px-4 py-2 text-sm text-right">
                    <a class="text-primary-700 hover:text-primary-600" href="{{ route('invoices.show', $f['id']) }}">Détails</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-1">Aucune facture trouvée</p>
                        <p class="text-gray-500">Commencez par créer votre première facture.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
@if($invoices->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de {{ $invoices->firstItem() }} à {{ $invoices->lastItem() }} sur {{ $invoices->total() }} résultats
        </div>
        <div class="pagination-links">
            {{ $invoices->links('pagination::tailwind') }}
        </div>
    </div>
@endif

<style>
/* Styles pour la pagination bleue */
.pagination-links .relative {
  @apply inline-flex items-center;
}

.pagination-links a, .pagination-links span {
  @apply px-3 py-2 text-sm leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-blue-50 hover:text-blue-600;
}

.pagination-links .bg-blue-50 {
  @apply bg-blue-600 text-white border-blue-600;
}

.pagination-links a:first-child, .pagination-links span:first-child {
  @apply rounded-l-lg;
}

.pagination-links a:last-child, .pagination-links span:last-child {
  @apply rounded-r-lg;
}
</style>
