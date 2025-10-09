@php
    // Defaults for optional variables
    $action = $action ?? '#';
    $method = strtoupper($method ?? 'POST');
    $invoice = $invoice ?? null;
    $quoteData = $quoteData ?? null;
@endphp

@if($quoteData)
<!-- Message d'information sur le devis -->
<div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-blue-900 mb-1">📋 Conversion du devis {{ $quoteData['quote_numero'] }}</h3>
            <p class="text-sm text-blue-700">
                Les informations du devis ont été pré-remplies. Ajoutez simplement le <strong>téléphone du client</strong> et validez la facture.
            </p>
        </div>
    </div>
</div>
@endif

<form action="{{ route('invoices.store') }}" method="POST" class="space-y-6" id="invoiceForm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-semibold mb-1">Veuillez corriger les erreurs suivantes :</div>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header fields -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 md:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de facture</label>
                <input name="invoice_date" type="date"
                    value="{{ old('invoice_date', $quoteData['date_devis'] ?? (isset($invoice) ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : now()->format('Y-m-d'))) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2 md:py-3" />

                @error('invoice_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Client (Nom)</label>
                <input name="client_name" type="text" value="{{ old('client_name', $quoteData['client_nom'] ?? ($invoice->client_name ?? '')) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2 md:py-3 {{ $quoteData ? 'bg-gray-50' : '' }}"
                    placeholder="Acme SARL" {{ $quoteData ? 'readonly' : '' }} />
                @error('client_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if($quoteData)
                    <p class="mt-1 text-xs text-gray-500">Nom du client depuis le devis</p>
                @endif
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Client (Téléphone) *</label>
                <input name="client_phone" type="tel"
                    value="{{ old('client_phone', $invoice->client_phone ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2 md:py-3 {{ $quoteData ? 'border-orange-300 focus:ring-orange-500 focus:border-orange-500' : '' }}"
                    placeholder="+237 6XX XX XX XX" required />
                @error('client_phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if($quoteData)
                    <p class="mt-1 text-xs text-orange-600 font-semibold">⚠️ Champ à remplir obligatoirement</p>
                @endif
            </div>
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendeur</label>
                <input name="vendor_name" type="text" value="{{ old('vendor_name', $invoice->vendor_name ?? auth()->user()->name) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2 md:py-3"
                    placeholder="{{ auth()->user()->name }}" readonly />
                @error('vendor_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Vendeur connecté automatiquement renseigné</p>
            </div>
        </div>
    </div>

    <!-- Products lines -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="font-heading text-lg text-gray-900">Lignes de facture</h3>
            <button type="button" id="addLine" class="px-3 py-2 rounded-lg border hover:bg-gray-50 w-full sm:w-auto">Ajouter une ligne</button>
        </div>

        <div style="overflow-x: auto; overflow-y: visible; position: relative;">
            <table class="min-w-full divide-y divide-gray-200" id="linesTable" style="table-layout: fixed; width: 100%;">
                <thead class="bg-gray-50">
                    <tr>
                        <th style="width: 35%; min-width: 200px;" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th style="width: 15%; min-width: 100px;" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                        <th style="width: 20%; min-width: 120px;" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                        <th style="width: 20%; min-width: 120px;" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th style="width: 10%; min-width: 80px;" class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @php
                        $oldLines = old('lines');
                        
                        // Priorité 1: Données du devis
                        if (!$oldLines && $quoteData && isset($quoteData['items'])) {
                            $oldLines = array_map(function ($item) {
                                return [
                                    'product_id' => $item['product_id'],
                                    'quantity' => $item['quantite'],
                                    'unit_price' => $item['prix_unitaire'],
                                    'total_price' => $item['prix_total'],
                                ];
                            }, $quoteData['items']);
                        }
                        
                        // Priorité 2: Données de la facture existante
                        if (!$oldLines && isset($invoice)) {
                            $oldLines = $invoice->products
                                ->map(function ($p) {
                                    return [
                                        'product_id' => $p->id,
                                        'quantity' => $p->pivot->quantity,
                                        'unit_price' => $p->pivot->unit_price,
                                        'total_price' => $p->pivot->total_price,
                                    ];
                                })
                                ->toArray();
                        }
                    @endphp
                    @if ($oldLines)
                        @foreach ($oldLines as $i => $line)
                            <tr style="position: relative;">
                                <td class="px-3 py-2" style="overflow: visible;">
                                    <select name="lines[{{ $i }}][product_id]"
                                        class="product-select w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2"
                                        data-index="{{ $i }}">
                                        <option value="">— Sélectionner —</option>
                                        @foreach ($products as $prod)
                                            <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}"
                                                {{ (string) ($line['product_id'] ?? '') === (string) $prod->id ? 'selected' : '' }}>
                                                {{ $prod->nom }} ({{ $prod->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("lines.$i.product_id")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-2 py-2">
                                    <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: center; width: 100%;">
                                        <button type="button" class="qty-decrease" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #ef4444; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">−</button>
                                        <input name="lines[{{ $i }}][quantity]" type="text"
                                            value="{{ $line['quantity'] ?? 1 }}"
                                            placeholder="1"
                                            style="width: 50px; text-align: center; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem; font-size: 0.875rem;"
                                            class="line-qty focus:ring-primary-600 focus:border-primary-600" />
                                        <input type="hidden" name="lines[{{ $i }}][quantite_decimal]" class="quantite-decimal">
                                        <button type="button" class="qty-increase" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #10b981; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">+</button>
                                    </div>
                                    @error("lines.$i.quantity")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input name="lines[{{ $i }}][unit_price]" type="number" step="0.01"
                                        value="{{ $line['unit_price'] ?? 0 }}"
                                        class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2" />
                                    @error("lines.$i.unit_price")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input name="lines[{{ $i }}][total_price]" type="number" step="0.01"
                                        value="{{ $line['total_price'] ?? 0 }}"
                                        class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2"
                                        readonly />
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <button type="button"
                                        class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line text-xs">Suppr.</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr style="position: relative;">
                            <td class="px-3 py-2" style="overflow: visible;">
                                <select name="lines[0][product_id]"
                                    class="product-select w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2"
                                    data-index="0">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}">
                                            {{ $prod->nom }} ({{ $prod->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2">
                                <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: center; width: 100%;">
                                    <button type="button" class="qty-decrease" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #ef4444; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;">−</button>
                                    <input name="lines[0][quantity]" type="text"
                                        value="1"
                                        placeholder="1"
                                        style="width: 50px; text-align: center; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem; font-size: 0.875rem;"
                                        class="line-qty focus:ring-primary-600 focus:border-primary-600" />
                                    <input type="hidden" name="lines[0][quantite_decimal]" class="quantite-decimal">
                                    <button type="button" class="qty-increase" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #10b981; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;">+</button>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <input name="lines[0][unit_price]" type="number" step="0.01" value="0"
                                    class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2"
                                    readonly />
                            </td>
                            <td class="px-3 py-2">
                                <input name="lines[0][total_price]" type="number" step="0.01" value="0"
                                    class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2"
                                    readonly />
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button"
                                    class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line text-xs">Suppr.</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Totals summary -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-end">
                <div class="w-full sm:w-auto">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">Montant total</div>
                            <div class="font-heading text-2xl text-gray-900">
                                <span id="total_display">0.00</span>
                                <span class="text-base text-gray-500">{{ $currency ?? 'FCFA' }}</span>
                            </div>
                        </div>
                        <input type="hidden" id="total_amount" name="total_amount"
                            value="{{ old('total_amount', $quoteData['total_amount'] ?? ($invoice->total_amount ?? 0)) }}" />
                        @error('total_amount')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-lg border w-full sm:w-auto text-center">Annuler</a>
        <button type="submit" id="submitBtn"
    class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">
    Enregistrer
</button>
    </div>
</form>

<script>
    // 🔥 Protection contre les doubles clics
    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        
        // Si déjà en cours de soumission, bloquer
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        // Désactiver le bouton
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Enregistrement...';
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    });
</script>

<template id="linePrototype">
    <tr style="position: relative;">
        <td class="px-3 py-2" style="overflow: visible;">
            <select name="__NAME__[product_id]"
                class="product-select w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2"
                data-index="__INDEX__">
                <option value="">— Sélectionner —</option>
                @foreach ($products as $prod)
                    <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}">
                        {{ $prod->nom }} ({{ $prod->sku }})
                    </option>
                @endforeach
            </select>
        </td>
        <td class="px-2 py-2">
            <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: center; width: 100%;">
                <button type="button" class="qty-decrease" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #ef4444; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">−</button>
                <input name="__NAME__[quantity]" type="text"
                    value="1"
                    placeholder="1"
                    style="width: 50px; text-align: center; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem; font-size: 0.875rem;"
                    class="line-qty focus:ring-primary-600 focus:border-primary-600" />
                <input type="hidden" name="__NAME__[quantite_decimal]" class="quantite-decimal">
                <button type="button" class="qty-increase" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; background-color: #10b981; color: white; border-radius: 0.375rem; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; flex-shrink: 0;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">+</button>
            </div>
        </td>
        <td class="px-3 py-2">
            <input name="__NAME__[unit_price]" type="number" step="0.01" value="0"
                class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2" />
        </td>
        <td class="px-3 py-2">
            <input name="__NAME__[total_price]" type="number" step="0.01" value="0"
                class="w-full text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2"
                readonly />
        </td>
        <td class="px-3 py-2 text-right">
            <button type="button"
                class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line text-xs">Suppr.</button>
        </td>
    </tr>
</template>


@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<style>
    /* 🔥 FIX DROPDOWN CHOICES.JS */
    .choices {
        position: relative !important;
        width: 100% !important;
        z-index: 100 !important;
    }
    
    .choices__inner {
        min-height: 40px !important;
        padding: 6px 12px !important;
    }
    
    /* Dropdown toujours vers le bas */
    .choices__list--dropdown {
        position: fixed !important; /* 🔥 FIXED au lieu de absolute */
        z-index: 99999 !important;
        margin-top: 4px !important;
        max-height: 300px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: white !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        /* Empêcher le scroll de la page de faire scroller le dropdown */
        overscroll-behavior: contain !important;
    }
    
    .choices__list--dropdown.is-active {
        display: block !important;
    }
    
    /* Empêcher le flip vers le haut */
    .choices.is-flipped .choices__list--dropdown {
        top: auto !important;
        bottom: auto !important;
    }
    
    /* Items du dropdown */
    .choices__list--dropdown .choices__item {
        padding: 8px 10px !important;
        font-size: 14px !important;
    }
    
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    
    /* Champ de recherche */
    .choices__input {
        background-color: white !important;
        padding: 8px 10px !important;
        font-size: 14px !important;
        margin-bottom: 0 !important;
    }
    
    /* Table overflow fix */
    #linesTable tbody tr {
        position: relative !important;
    }
    
    #linesTable tbody td:first-child {
        overflow: visible !important;
        min-width: 200px !important;
    }
    
    /* 🔥 Boutons + et - sur la même ligne avec hover */
    .qty-decrease:hover {
        background-color: #dc2626 !important;
    }
    
    .qty-increase:hover {
        background-color: #059669 !important;
    }
    
    /* Responsive table adjustments */
    @media (max-width: 768px) {
        #linesTable thead th,
        #linesTable tbody td {
            padding: 8px 6px !important;
            font-size: 0.75rem !important;
        }
        
        #linesTable thead th {
            white-space: nowrap;
        }
        
        .choices__inner {
            padding: 4px 8px !important;
            min-height: 36px !important;
        }
        
        .choices__input {
            padding: 4px 8px !important;
            font-size: 0.75rem !important;
        }
        
        .choices__list--dropdown .choices__item {
            padding: 6px 8px !important;
            font-size: 0.75rem !important;
        }
    }
    
    @media (max-width: 640px) {
        #linesTable {
            font-size: 0.75rem;
        }
        
        #linesTable thead th,
        #linesTable tbody td {
            padding: 6px 4px !important;
        }
        
        .line-qty {
            width: 40px !important;
            padding: 0.25rem !important;
            font-size: 0.7rem !important;
        }
        
        .qty-decrease,
        .qty-increase {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            min-height: 24px !important;
            font-size: 0.8rem !important;
        }
        
        .line-price,
        .line-total {
            padding: 0.25rem !important;
            font-size: 0.7rem !important;
        }
        
        .remove-line {
            padding: 0.1rem 0.25rem !important;
            font-size: 0.65rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    (function() {
        const table = document.getElementById('linesTable');
        const addBtn = document.getElementById('addLine');
        const proto = document.getElementById('linePrototype');
        const totalInput = document.getElementById('total_amount');
        const totalDisplay = document.getElementById('total_display');
        
        const choicesInstances = new Map();

        function format(n) {
            return Number(n || 0);
        }

        function parseFraction(input) {
            const value = input.trim();
            if (!value) return 0;
            
            if (!value.includes('/')) {
                return parseFloat(value) || 0;
            }
            
            const parts = value.split('/');
            if (parts.length === 2) {
                const numerator = parseFloat(parts[0]);
                const denominator = parseFloat(parts[1]);
                if (!isNaN(numerator) && !isNaN(denominator) && denominator !== 0) {
                    return numerator / denominator;
                }
            }
            return 0;
        }

        function recalcRow(row) {
            const qtyInput = row.querySelector('.line-qty');
            const quantiteDecimalInput = row.querySelector('.quantite-decimal');
            
            const qty = parseFraction(qtyInput.value);
            
            if (quantiteDecimalInput) {
                quantiteDecimalInput.value = qty;
            }
            
            const price = format(row.querySelector('.line-price')?.value);
            const total = row.querySelector('.line-total');
            const t = (qty * price).toFixed(2);
            if (total) total.value = t;
        }

        function recalcAll() {
            const totals = Array.from(table.querySelectorAll('.line-total')).map(i => format(i.value));
            const sum = totals.reduce((a, b) => a + b, 0).toFixed(2);
            if (totalInput) totalInput.value = sum;
            if (totalDisplay) totalDisplay.textContent = Number(sum).toFixed(2);
        }

        function initChoicesForSelect(selectElement) {
            if (!selectElement) return null;
            
            // Vérifier que le select a des options
            if (selectElement.options.length === 0) {
                console.error('Le select n\'a pas d\'options');
                return null;
            }
            
            const choices = new Choices(selectElement, {
                searchEnabled: true,
                searchPlaceholderValue: 'Rechercher un produit...',
                noResultsText: 'Aucun produit trouvé',
                itemSelectText: '',
                shouldSort: false,
                removeItemButton: false,
                position: 'bottom',
                flip: false,
                searchFields: ['label', 'customProperties.sku'],
                fuseOptions: {
                    threshold: 0.3,
                    keys: ['label', 'customProperties.sku']
                }
            });
            
            // 🔥 Positionner le dropdown correctement en mode fixed
            selectElement.addEventListener('showDropdown', function() {
                setTimeout(() => {
                    const dropdown = selectElement.closest('.choices').querySelector('.choices__list--dropdown');
                    const choicesElement = selectElement.closest('.choices');
                    
                    if (dropdown && choicesElement) {
                        const rect = choicesElement.getBoundingClientRect();
                        dropdown.style.position = 'fixed';
                        dropdown.style.top = (rect.bottom + 4) + 'px';
                        dropdown.style.left = rect.left + 'px';
                        dropdown.style.width = rect.width + 'px';
                        dropdown.style.maxHeight = '300px';
                    }
                }, 0);
            });
            
            // 🔥 Repositionner lors du scroll
            window.addEventListener('scroll', function() {
                const choicesElement = selectElement.closest('.choices');
                if (choicesElement && choicesElement.classList.contains('is-open')) {
                    const dropdown = choicesElement.querySelector('.choices__list--dropdown');
                    if (dropdown) {
                        const rect = choicesElement.getBoundingClientRect();
                        dropdown.style.top = (rect.bottom + 4) + 'px';
                        dropdown.style.left = rect.left + 'px';
                    }
                }
            }, { passive: true });
            
            const index = selectElement.getAttribute('data-index');
            if (index) {
                choicesInstances.set(index, choices);
            }
            
            return choices;
        }

        function bindRowEvents(row) {
            const qtyInput = row.querySelector('.line-qty');
            const priceInput = row.querySelector('.line-price');
            const productSelect = row.querySelector('.product-select');
            const decreaseBtn = row.querySelector('.qty-decrease');
            const increaseBtn = row.querySelector('.qty-increase');

            const choicesInstance = initChoicesForSelect(productSelect);

            if (decreaseBtn) {
                decreaseBtn.addEventListener('click', () => {
                    const currentValue = parseFraction(qtyInput.value) || 1;
                    if (currentValue > 1) {
                        qtyInput.value = currentValue - 1;
                        recalcRow(row);
                        recalcAll();
                    }
                });
            }

            if (increaseBtn) {
                increaseBtn.addEventListener('click', () => {
                    const currentValue = parseFraction(qtyInput.value) || 0;
                    qtyInput.value = currentValue + 1;
                    recalcRow(row);
                    recalcAll();
                });
            }

            [qtyInput, priceInput].forEach(el => {
                if (el) {
                    el.addEventListener('input', () => {
                        recalcRow(row);
                        recalcAll();
                    });
                }
            });

            if (productSelect) {
                productSelect.addEventListener('change', (e) => {
                    const selectedOption = e.target.selectedOptions[0];
                    const unitPrice = selectedOption?.getAttribute('data-price') || 0;

                    if (priceInput) {
                        priceInput.value = parseFloat(unitPrice).toFixed(2);
                    }

                    recalcRow(row);
                    recalcAll();
                    
                    if (choicesInstance) {
                        setTimeout(() => {
                            choicesInstance.hideDropdown();
                        }, 100);
                    }
                });
            }

            row.querySelector('.remove-line')?.addEventListener('click', () => {
                const index = productSelect?.getAttribute('data-index');
                if (index && choicesInstances.has(index)) {
                    choicesInstances.get(index).destroy();
                    choicesInstances.delete(index);
                }
                
                row.remove();
                recalcAll();
            });

            recalcRow(row);
            recalcAll();
        }

        document.addEventListener('DOMContentLoaded', function() {
            Array.from(table.querySelectorAll('tbody tr')).forEach(bindRowEvents);
        });

        addBtn?.addEventListener('click', () => {
            const idx = table.querySelectorAll('tbody tr').length;
            const html = proto.innerHTML
                .replaceAll('__NAME__', `lines[${idx}]`)
                .replaceAll('__INDEX__', idx);
            const temp = document.createElement('tbody');
            temp.innerHTML = html.trim();
            const newRow = temp.firstElementChild;
            table.querySelector('tbody').appendChild(newRow);
            bindRowEvents(newRow);
        });
    })();
</script>
@endpush