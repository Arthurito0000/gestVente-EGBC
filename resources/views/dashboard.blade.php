@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec filtres de période -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard {{ $user_role }}</h1>
                <p class="text-gray-600 mt-1">
                    Période : {{ ucfirst($period) }} 
                    ({{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }})
                </p>
            </div>
            
            <!-- Filtres de période et produit -->
            <div class="flex flex-wrap gap-2">
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-2" id="periodForm">
                    <select name="period" onchange="toggleCustomDates()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="day" {{ $period === 'day' ? 'selected' : '' }}>📅 Aujourd'hui</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>📅 Cette semaine</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>📅 Ce mois</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>📅 Ce trimestre</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>📅 Cette année</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>📅 Personnalisé</option>
                    </select>
                    
                    <div id="customDates" class="flex gap-2 {{ $period !== 'custom' ? 'hidden' : '' }}">
                        <input type="date" name="start_date" value="{{ $start_date }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="date" name="end_date" value="{{ $end_date }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>

                    <!-- Filtre par produit -->
                    <select name="product_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[240px]">
                        <option value="">🔎 Tous les produits</option>
                        @foreach(($products_list ?? []) as $p)
                            <option value="{{ $p->id }}" {{ (string)($product_id ?? '') === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->sku }} - {{ $p->nom }}
                            </option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        Actualiser
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        @include('dashboard.admin')
    @elseif(auth()->user()->isStockManager())
        @include('dashboard.stock-manager')
    @elseif(auth()->user()->isSeller())
        @include('dashboard.seller')
    @else
        @include('dashboard.basic')
    @endif
</div>

<script>
function toggleCustomDates() {
    const period = document.querySelector('select[name="period"]').value;
    const customDates = document.getElementById('customDates');
    
    if (period === 'custom') {
        customDates.classList.remove('hidden');
    } else {
        customDates.classList.add('hidden');
        // Auto-submit pour les périodes prédéfinies
        document.getElementById('periodForm').submit();
    }
}

// Auto-submit quand on change la période (sauf custom)
document.querySelector('select[name="period"]').addEventListener('change', function() {
    if (this.value !== 'custom') {
        document.getElementById('periodForm').submit();
    }
});
</script>
@endsection
