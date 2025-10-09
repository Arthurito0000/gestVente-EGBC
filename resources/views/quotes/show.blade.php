@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête avec actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Devis {{ $quote->numero_devis }}</h1>
                <p class="text-gray-600 mt-1">Créé le {{ $quote->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('quotes.edit', $quote) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Modifier
                </a>
                
                <a href="{{ route('quotes.print', $quote) }}" 
                   target="_blank"
                   class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Imprimer
                </a>
                
                <!-- MODIFIÉ: Remplacer le lien par un formulaire avec loader -->
                <form id="pdfDownloadForm" action="{{ route('quotes.download-pdf', $quote) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" 
                            id="pdfDownloadBtn"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200 inline-flex items-center">
                        <svg id="pdfIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <svg id="pdfSpinner" class="hidden animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="pdfBtnText">Télécharger PDF</span>
                    </button>
                </form>
                
                <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Créer Facture
                    </button>
                </form>
                
                <a href="{{ route('quotes.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations client -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations Client</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Client</p>
                            <p class="font-semibold text-gray-900">{{ $quote->client_nom }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date du Devis</p>
                            <p class="font-semibold text-gray-900">{{ $quote->date_devis->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Vendeur</p>
                            <p class="font-semibold text-gray-900">{{ $quote->user->name }}</p>
                        </div>
                    </div>
                    
                    @if($quote->objet)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Objet</p>
                        <p class="text-gray-900">{{ $quote->objet }}</p>
                    </div>
                    @endif
                </div>

                <!-- Articles -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Articles du Devis</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">P.U</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">P.T</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($quote->items as $index => $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->designation }}</td>
                                    <td class="px-4 py-3 text-center text-sm text-gray-900">{{ $item->getQuantiteFormatted() }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} Fcfa</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($item->prix_total, 0, ',', ' ') }} Fcfa</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Résumé -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Totaux -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Totaux</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-gray-600">Total Matériel:</span>
                            <span class="font-semibold text-blue-600">{{ $quote->getTotalMaterielFormatte() }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-gray-600">Main d'Œuvre:</span>
                            <span class="font-semibold text-orange-600">{{ $quote->getMainOeuvreFormatte() }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-bold text-gray-800">TOTAL:</span>
                            <span class="text-xl font-bold text-green-600">{{ $quote->getTotalGeneralFormatte() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions Rapides</h2>
                    <div class="space-y-2">
                        <form action="{{ route('quotes.duplicate', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                📋 Dupliquer ce devis
                            </button>
                        </form>
                        
                        <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                                🗑️ Supprimer ce devis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJOUTÉ: Script pour le loader du bouton PDF -->
@push('scripts')
<script>
document.getElementById('pdfDownloadForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('pdfDownloadBtn');
    const icon = document.getElementById('pdfIcon');
    const spinner = document.getElementById('pdfSpinner');
    const text = document.getElementById('pdfBtnText');
    
    // Afficher le loader
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    icon.classList.add('hidden');
    spinner.classList.remove('hidden');
    text.textContent = 'Génération en cours...';
    
    // Réinitialiser après 5 secondes
    setTimeout(function() {
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        icon.classList.remove('hidden');
        spinner.classList.add('hidden');
        text.textContent = 'Télécharger PDF';
    }, 5000);
});
</script>
@endpush

@endsection