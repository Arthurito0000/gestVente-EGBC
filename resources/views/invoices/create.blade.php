@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 px-4 py-6">
        <div>
            <h1 class="font-heading text-2xl text-gray-900">Créer une facture</h1>
            <p class="text-gray-500">Créez une facture pour des sortie de stock.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-4 md:p-6">
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('invoices.partials.form')
            </form>
        </div>
    </div>
@endsection