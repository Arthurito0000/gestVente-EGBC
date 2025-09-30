@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <!-- En-tête -->
  <div class="bg-white rounded-2xl shadow-md p-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">➕ Créer un produit</h1>
        <p class="text-gray-600 mt-1">Ajoutez un nouveau produit au catalogue avec son stock initial</p>
      </div>
      <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
        ← Retour à la liste
      </a>
    </div>
  </div>

  <!-- Messages de succès/erreur -->
  @if(session('success'))
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
    {{ session('success') }}
  </div>
  @endif

  @if(session('error'))
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
    {{ session('error') }}
  </div>
  @endif

  <!-- Formulaire -->
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
