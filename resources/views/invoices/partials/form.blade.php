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
        <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input name="code" type="text" value="{{ old('code', $invoice->code ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3"
                    placeholder="INV-CM-0001" />
                @error('code')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div> --}}
            {{-- <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de facture</label>
                <input name="invoice_number" type="text"
                    value="{{ old('invoice_number', $invoice->invoice_number ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3"
                    placeholder="INV-2025-0001" />
                @error('invoice_number')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div> --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date de facture</label>
                <input name="invoice_date" type="date"
                    value="{{ old('invoice_date', $quoteData['date_devis'] ?? (isset($invoice) ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : now()->format('Y-m-d'))) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3" />

                @error('invoice_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client (Nom)</label>
                <input name="client_name" type="text" value="{{ old('client_name', $quoteData['client_nom'] ?? ($invoice->client_name ?? '')) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3 {{ $quoteData ? 'bg-gray-50' : '' }}"
                    placeholder="Acme SARL" {{ $quoteData ? 'readonly' : '' }} />
                @error('client_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if($quoteData)
                    <p class="mt-1 text-xs text-gray-500">Nom du client depuis le devis</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client (Téléphone) *</label>
                <input name="client_phone" type="tel"
                    value="{{ old('client_phone', $invoice->client_phone ?? '') }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3 {{ $quoteData ? 'border-orange-300 focus:ring-orange-500 focus:border-orange-500' : '' }}"
                    placeholder="+237 6XX XX XX XX" required />
                @error('client_phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @if($quoteData)
                    <p class="mt-1 text-xs text-orange-600 font-semibold">⚠️ Champ à remplir obligatoirement</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendeur</label>
                <input name="vendor_name" type="text" value="{{ old('vendor_name', $invoice->vendor_name ?? auth()->user()->name) }}"
                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-3"
                    placeholder="{{ auth()->user()->name }}" readonly />
                @error('vendor_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Vendeur connecté automatiquement renseigné</p>
            </div>



        </div>
    </div>

    <!-- Products lines -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-heading text-lg text-gray-900">Lignes de facture</h3>
            <button type="button" id="addLine" class="px-3 py-2 rounded-lg border hover:bg-gray-50">Ajouter une
                ligne</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="linesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qté</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix Unitaire</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-3 py-2"></th>
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
                            <tr>
                                <td class="px-3 py-2">
                                    <select name="lines[{{ $i }}][product_id]"
                                        class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2.5">
                                        <option value="">— Sélectionner —</option>
                                        @foreach ($products as $prod)
                                            <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}"
                                                {{ (string) ($line['product_id'] ?? '') === (string) $prod->id ? 'selected' : '' }}>
                                                {{ $prod->sku }} — {{ $prod->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("lines.$i.product_id")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex items-center space-x-1">
                                        <button type="button" class="qty-decrease w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold flex items-center justify-center">−</button>
                                        <input name="lines[{{ $i }}][quantity]" type="number" min="1"
                                            value="{{ $line['quantity'] ?? 1 }}"
                                            class="w-16 text-center rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-qty px-2 py-1.5 text-sm" />
                                        <button type="button" class="qty-increase w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold flex items-center justify-center">+</button>
                                    </div>
                                    @error("lines.$i.quantity")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input name="lines[{{ $i }}][unit_price]" type="number" step="0.01"
                                        value="{{ $line['unit_price'] ?? 0 }}"
                                        class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2.5" />
                                    @error("lines.$i.unit_price")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input name="lines[{{ $i }}][total_price]" type="number" step="0.01"
                                        value="{{ $line['total_price'] ?? 0 }}"
                                        class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2.5"
                                        readonly />
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <button type="button"
                                        class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line">Supprimer</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="px-3 py-2">
                                <select name="lines[0][product_id]"
                                    class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2.5">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}">
                                            {{ $prod->sku }} — {{ $prod->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex items-center space-x-1">
                                    <button type="button" class="qty-decrease w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold flex items-center justify-center">−</button>
                                    <input name="lines[0][quantity]" type="number" min="1" value="1"
                                        class="w-16 text-center rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-qty px-2 py-1.5 text-sm" />
                                    <button type="button" class="qty-increase w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold flex items-center justify-center">+</button>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <input name="lines[0][unit_price]" type="number" step="0.01" value="0"
                                    class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2.5"
                                    readonly />
                            </td>
                            <td class="px-3 py-2">
                                <input name="lines[0][total_price]" type="number" step="0.01" value="0"
                                    class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2.5"
                                    readonly />
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button"
                                    class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line">Supprimer</button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Totals summary (visible below the list) -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-end">
                <div class="w-full md:w-auto md:min-w-[340px]">
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

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-lg border">Annuler</a>
        <button type="submit"
            class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-5 py-2.5">Enregistrer</button>
    </div>
</form>

<template id="linePrototype">
    <tr>
        <td class="px-3 py-2">
            <select name="__NAME__[product_id]"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 px-3 py-2.5">
                <option value="">— Sélectionner —</option>
                @foreach ($products as $prod)
                    <option value="{{ $prod->id }}" data-price="{{ $prod->prix_vente }}">{{ $prod->sku }} —
                        {{ $prod->nom }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <div class="flex items-center space-x-1">
                <button type="button" class="qty-decrease w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-bold flex items-center justify-center">−</button>
                <input name="__NAME__[quantity]" type="number" min="1" value="1"
                    class="w-16 text-center rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-qty px-2 py-1.5 text-sm" />
                <button type="button" class="qty-increase w-6 h-6 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-bold flex items-center justify-center">+</button>
            </div>
        </td>
        <td class="px-3 py-2">
            <input name="__NAME__[unit_price]" type="number" step="0.01" value="0"
                class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-price px-3 py-2.5" />
        </td>
        <td class="px-3 py-2">
            <input name="__NAME__[total_price]" type="number" step="0.01" value="0"
                class="w-32 text-right rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600 line-total px-3 py-2.5"
                readonly />
        </td>
        <td class="px-3 py-2 text-right">
            <button type="button"
                class="px-2 py-1 rounded border text-red-600 hover:bg-red-50 remove-line">Supprimer</button>
        </td>
    </tr>
</template>

<script>
    (function() {
        const table = document.getElementById('linesTable');
        const addBtn = document.getElementById('addLine');
        const proto = document.getElementById('linePrototype');
        const totalInput = document.getElementById('total_amount');
        const totalDisplay = document.getElementById('total_display');

        function format(n) {
            return Number(n || 0);
        }

        function recalcRow(row) {
            const qty = format(row.querySelector('.line-qty')?.value);
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

        function bindRowEvents(row) {
            const qtyInput = row.querySelector('.line-qty');
            const priceInput = row.querySelector('.line-price');
            const productSelect = row.querySelector('select');
            const decreaseBtn = row.querySelector('.qty-decrease');
            const increaseBtn = row.querySelector('.qty-increase');

            // Boutons +/- pour la quantité
            if (decreaseBtn) {
                decreaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(qtyInput.value) || 1;
                    if (currentValue > 1) {
                        qtyInput.value = currentValue - 1;
                        recalcRow(row);
                        recalcAll();
                    }
                });
            }

            if (increaseBtn) {
                increaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(qtyInput.value) || 0;
                    qtyInput.value = currentValue + 1;
                    recalcRow(row);
                    recalcAll();
                });
            }

            // Update total when quantity or price changes
            [qtyInput, priceInput].forEach(el => {
                if (el) {
                    el.addEventListener('input', () => {
                        recalcRow(row);
                        recalcAll();
                    });
                }
            });

            // When product changes, auto-fill unit price
            if (productSelect) {
                productSelect.addEventListener('change', (e) => {
                    const selectedOption = e.target.selectedOptions[0];
                    const unitPrice = selectedOption?.getAttribute('data-price') || 0;

                    if (priceInput) {
                        priceInput.value = parseFloat(unitPrice).toFixed(2);
                    }

                    recalcRow(row);
                    recalcAll();
                });
            }

            // Remove line
            row.querySelector('.remove-line')?.addEventListener('click', () => {
                row.remove();
                recalcAll();
            });

            // Initial calculation
            recalcRow(row);
            recalcAll();
        }


        // Bind existing rows
        Array.from(table.querySelectorAll('tbody tr')).forEach(bindRowEvents);

        addBtn?.addEventListener('click', () => {
            const idx = table.querySelectorAll('tbody tr').length;
            const html = proto.innerHTML.replaceAll('__NAME__', `lines[${idx}]`);
            const temp = document.createElement('tbody');
            temp.innerHTML = html.trim();
            const newRow = temp.firstElementChild;
            table.querySelector('tbody').appendChild(newRow);
            bindRowEvents(newRow);
        });
    })();
</script>
