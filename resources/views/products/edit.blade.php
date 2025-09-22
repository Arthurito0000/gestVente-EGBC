@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div>
    <h1 class="font-heading text-2xl text-gray-900">Modifier le produit</h1>
    <p class="text-gray-500">Modifiez les informations du produit {{ $product->sku }}.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
      @csrf
      @method('PUT')
      @include('products.partials.form')
    </form>
  </div>
</div>
@endsection
