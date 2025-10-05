<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($quotes->count() > 0)
    <!-- Version mobile : cartes -->
    <div class="block md:hidden">
        @foreach($quotes as $quote)
        <div class="border-b border-gray-200 p-4 hover:bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-semibold text-gray-900">{{ $quote->numero_devis }}</div>
                    <div class="text-sm text-gray-600">{{ $quote->client_nom }}</div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600">{{ $quote->getTotalGeneralFormatte() }}</div>
                    <div class="text-xs text-gray-500">{{ $quote->date_devis->format('d/m/Y') }}</div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 mt-3">
                <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-800 p-2" title="Voir">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                <a href="{{ route('quotes.edit', $quote) }}" class="text-green-600 hover:text-green-800 p-2" title="Modifier">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="text-orange-600 hover:text-orange-800 p-2" 
                            title="Créer Facture">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Version desktop : tableau -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Devis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($quotes as $quote)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $quote->numero_devis }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900">{{ $quote->client_nom }}</div>
                        @if($quote->objet)
                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($quote->objet, 40) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $quote->date_devis->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ $quote->getTotalGeneralFormatte() }}</div>
                        <div class="text-xs text-gray-500">
                            Mat: {{ number_format($quote->total_materiel, 0, ',', ' ') }} | 
                            MO: {{ number_format($quote->main_oeuvre, 0, ',', ' ') }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <!-- Voir -->
                            <a href="{{ route('quotes.show', $quote) }}" 
                               class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition duration-200" 
                               title="Voir les détails">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>

                            <!-- Modifier -->
                            <a href="{{ route('quotes.edit', $quote) }}" 
                               class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition duration-200" 
                               title="Modifier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <!-- Imprimer -->
                            <a href="{{ route('quotes.print', $quote) }}" 
                               target="_blank"
                               class="text-purple-600 hover:text-purple-800 hover:bg-purple-50 p-2 rounded-lg transition duration-200" 
                               title="Imprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </a>

                            <!-- Créer Facture -->
                            <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="text-orange-600 hover:text-orange-800 hover:bg-orange-50 p-2 rounded-lg transition duration-200" 
                                        title="Créer Facture">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </button>
                            </form>

                            <!-- Supprimer -->
                            <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition duration-200" 
                                        title="Supprimer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($quotes->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200">
        {{ $quotes->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun devis trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau devis.</p>
        @can('create-sales')
        <div class="mt-6">
            <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouveau Devis
            </a>
        </div>
        @endcan
    </div>
    @endif
</div>
