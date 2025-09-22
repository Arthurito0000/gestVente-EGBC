@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
  <div>
    <h1 class="font-heading text-2xl text-gray-900">Enregistrer un mouvement</h1>
    <p class="text-gray-500">Créez une entrée ou une sortie de stock.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6 md:p-8">
    <form action="#" method="POST" class="space-y-6">
      @csrf
      @include('movements.partials.form')
    </form>
  </div>

  <!-- <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-900">
    <div class="font-medium mb-1">Spécification: Mouvement</div>
    <pre class="whitespace-pre-wrap">- id: int
- produit_id: int (FK)
- type: string ("ENTREE"/"SORTIE")
- quantite: int
- motif: string
- date: datetime
+ enregistrer(): void</pre>
  </div> -->
</div>
@endsection
