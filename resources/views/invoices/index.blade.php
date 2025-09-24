@extends('layouts.app')


@if (session('pdf_download'))
    <script>
        window.onload = function () {
            const link = document.createElement('a');
            link.href = "{{ session('pdf_download') }}";
            link.download = '';
            link.target = '_blank';
            link.click();
        };
    </script>
@endif


@section('content')
    <div class="space-y-6 w-full">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-heading text-2xl text-gray-900">Factures</h1>
                <p class="text-gray-500">Gestion des factures et lignes de facturation.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" placeholder="Rechercher…"
                    class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
                <a href="{{ route('invoices.create') }}"
                    class="bg-primary-600 hover:bg-primary-700 text-white rounded-lg px-4 py-2">Nouvelle facture</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="text-sm text-gray-600">Liste des factures</div>
                <div class="flex items-center gap-2 text-sm">
                    <button class="px-3 py-1 rounded border">Exporter</button>
                </div>
            </div>
            <div class="overflow-x-auto">
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
                        @foreach ($invoices as $f)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $f['code'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $f['invoice_date'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $f['client_name'] }}</td>
                                <td class="px-4 py-2 text-sm italic text-gray-700">{{ $f['total_amount'] }} FCFA</td>
                                {{-- <td class="px-4 py-2 text-sm text-right text-gray-900">
                                    {{ number_format($f['total'], 2, ',', ' ') }} €</td> --}}
                                <td class="px-4 py-2 text-sm text-right"><a class="text-primary-700 hover:text-primary-600"
                                        href="{{ route('invoices.show', $f['id']) }}">Détails</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
