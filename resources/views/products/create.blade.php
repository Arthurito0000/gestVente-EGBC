@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div>
    <h1 class="font-heading text-2xl text-gray-900">Créer un produit</h1>
    <p class="text-gray-500">Ajoutez un nouveau produit au catalogue.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
    <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
      @csrf
      @include('products.partials.form')
    </form>
  </div>


  <!-- <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-900">
    <div class="font-medium mb-1">Spécification: Produit</div>
    <pre class="whitespace-pre-wrap">- id: int
- sku: string
- nom: string
- prix_achat: float
- prix_vente: float
+ creer(): void
+ modifier(): void</pre>
  </div> -->
</div>
@endsection
