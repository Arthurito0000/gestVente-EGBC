@extends('layouts.app')

@section('content')
    @php
        // Ensure products are available
        $invoice->loadMissing('products');
        $currency = $invoice->currency ?? 'FCFA';
        $date = $invoice->invoice_date
            ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d/m/Y')
            : $invoice->created_at?->format('d/m/Y');
        $total = (float) ($invoice->total_amount ?? 0);
    @endphp


    <div class="space-y-6">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl text-gray-900">Facture {{ $invoice->invoice_number }}</h1>
                <p class="text-gray-500">Détails de la facture — {{ $date }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 2.19L8.75 6.5m12.5 3.75h-2.25m-12.5 0h2.25" />
                    </svg>
                    Imprimer
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="inline-flex items-center gap-2 border rounded-lg px-4 py-2 hover:bg-gray-50">Retour</a>
            </div>
        </div>

        <!-- Info cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                <div class="text-sm text-gray-500 mb-2">Informations facture</div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Code</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->code }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Numéro</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->invoice_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Date</dt>
                        <dd class="font-medium text-gray-900">{{ $date }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Devise</dt>
                        <dd class="font-medium text-gray-900">{{ $currency }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                <div class="text-sm text-gray-500 mb-2">Client</div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Nom</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->client_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Localisation</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->client_location }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                <div class="text-sm text-gray-500 mb-2">Vendeur</div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Nom</dt>
                        <dd class="font-medium text-gray-900">{{ $invoice->vendor_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-600">Montant total</dt>
                        <dd class="font-heading text-xl text-gray-900">{{ number_format($total, 2, ',', ' ') }}
                            {{ $currency }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Lines table -->
        <div class="bg-white rounded-2xl shadow-md border mb-20 border-gray-200 overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="text-sm text-gray-600">Lignes de facture</div>
                <div class="text-sm text-gray-500">{{ $invoice->products->count() }} articles</div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ref.</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qté</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">PU</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @php $sum = 0; @endphp
                        @foreach ($invoice->products as $p)
                            @php
                                $qty = (int) ($p->pivot->quantity ?? 0);
                                $unit = (float) ($p->pivot->unit_price ?? 0);
                                $line = (float) ($p->pivot->total_price ?? $qty * $unit);
                                $sum += $line;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm font-mono text-gray-700">{{ $p->sku }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $p->nom }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-700">{{ $qty }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-700">
                                    {{ number_format($unit, 2, ',', ' ') }}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900">
                                    {{ number_format($line, 2, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals summary -->
            <div class="p-4 border-t">
                <div class="w-full md:w-auto md:min-w-[320px] ml-auto">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm text-gray-600">Sous-total</div>
                            <div class="text-sm text-gray-900">{{ number_format($sum, 2, ',', ' ') }} {{ $currency }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="font-heading text-base text-gray-900">Montant total</div>
                            <div class="font-heading text-xl text-gray-900">
                                {{ number_format($total ?: $sum, 2, ',', ' ') }} {{ $currency }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-20">
            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline pt-10 delete-form">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="delete-btn inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white rounded-lg px-4 py-2"
                    title="Supprimer la facture" data-invoice-number="{{ $invoice->invoice_number }}">
                    Supprimer
                </button>
            </form>
        </div>

        <!-- Modal de confirmation de suppression (identique au produits, adapté) -->
        <div id="deleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm transition-all duration-300">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div id="modalContent"
                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                    <!-- Bouton de fermeture -->
                    <button id="closeModal"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="p-8 text-center">
                        <!-- Icône d'alerte -->
                        <div
                            class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6 animate-pulse">
                            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Confirmer la suppression</h3>
                        <div class="mb-8">
                            <p class="text-gray-600 mb-2">Êtes-vous sûr de vouloir supprimer la facture</p>
                            <p class="font-semibold text-gray-900 text-lg" id="invoiceNumber"></p>
                            <p class="text-sm text-red-600 mt-3 bg-red-50 px-4 py-2 rounded-lg">⚠️ Cette action est
                                irréversible</p>
                        </div>
                        <div class="flex gap-3">
                            <button id="cancelDelete"
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 hover:scale-105">Annuler</button>
                            <button id="confirmDelete"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 hover:scale-105 shadow-lg">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('modalContent');
                const invoiceNumberSpan = document.getElementById('invoiceNumber');
                const cancelBtn = document.getElementById('cancelDelete');
                const confirmBtn = document.getElementById('confirmDelete');
                const closeBtn = document.getElementById('closeModal');
                let currentForm = null;

                document.querySelectorAll('.delete-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const number = this.getAttribute('data-invoice-number') || '';
                        currentForm = this.closest('.delete-form');
                        invoiceNumberSpan.textContent = number;
                        showModal();
                    });
                });

                cancelBtn.addEventListener('click', closeModal);
                closeBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                });

                confirmBtn.addEventListener('click', function() {
                    if (currentForm) {
                        confirmBtn.innerHTML =
                            '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                        confirmBtn.disabled = true;
                        setTimeout(() => {
                            currentForm.submit();
                        }, 500);
                    }
                });

                function showModal() {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }

                function closeModal() {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                        currentForm = null;
                        confirmBtn.innerHTML = 'Supprimer';
                        confirmBtn.disabled = false;
                    }, 300);
                }
            });
        </script>
    </div>
@endsection
