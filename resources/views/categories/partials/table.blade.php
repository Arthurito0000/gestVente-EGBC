<table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom catégorie</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ( $categories as $c)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $c['code'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $c['name'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $c['description'] }}</td>
                                <td class="px-4 py-2 text-sm text-right">
                                    <a href="#" class="text-primary-700 hover:text-primary-600">Détails</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
</table>

<!-- Pagination -->
@if($categories->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de {{ $categories->firstItem() }} à {{ $categories->lastItem() }} sur {{ $categories->total() }} résultats
        </div>
        <div class="pagination-links">
            {{ $categories->links('pagination::tailwind') }}
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