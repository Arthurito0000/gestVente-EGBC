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
        @foreach ($categories as $c)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $c['code'] }}</td>
                <td class="px-4 py-2 text-sm text-gray-900">{{ $c['name'] }}</td>
                <td class="px-4 py-2 text-sm text-gray-700">{{ $c['description'] }}</td>
                <td class="px-4 py-2 text-sm">
                    <div class="flex items-center justify-end space-x-1">
                        <a href="{{ route('categories.show', $c) }}" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors flex items-center justify-center" title="Voir les détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </a>

                        <button type="button" 
                            class="edit-category-btn p-1.5 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors flex items-center justify-center" 
                            title="Modifier la catégorie" 
                            data-id="{{ $c->id }}"
                            data-name="{{ $c->name }}"
                            data-description="{{ $c->description }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <form action="{{ route('categories.destroy', $c) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                class="delete-btn p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center"
                                title="Supprimer la catégorie" data-product-name="{{ $c->name }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination -->
@if ($categories->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Affichage de {{ $categories->firstItem() }} à {{ $categories->lastItem() }} sur {{ $categories->total() }}
            résultats
        </div>
        <div class="pagination-links">
            {{ $categories->links('vendor.pagination.simple-tailwind') }}
        </div>
    </div>
@endif
