@extends('layouts.app')
@section('title', 'Gestions des categories')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-heading text-2xl text-gray-900">Catégories</h1>
                <p class="text-gray-500">Gérez les catégories</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" placeholder="Rechercher…"
                    class="hidden md:block rounded-lg border-gray-300 focus:ring-primary-600 focus:border-primary-600" />
                @can('manage-categories')
                <button id="openCategoryModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2">
                    ➕ Ajouter catégorie
                </button>
                @endcan
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="text-sm text-gray-600">Liste des catégories</div>
                <div class="flex items-center gap-2 text-sm">
                    <button class="px-3 py-1 rounded border">Exporter</button>
                </div>
            </div>
            <div class="overflow-x-auto">
              @include('categories.partials.table')
            </div>
        </div>

        <!-- Modal: Ajouter Catégorie -->
        <div id="categoryModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 99998;">
          <div class="absolute inset-0 bg-black/50" id="modalBackdrop"></div>
          <div class="relative w-full max-w-lg p-4" style="z-index: 99999;">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-heading text-lg text-gray-900">Ajouter une catégorie</h3>
                <button id="closeCategoryModal" class="p-2 rounded hover:bg-gray-100" aria-label="Fermer">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" class="px-6 py-5 space-y-5">
                @csrf
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                  <input name="name" type="text" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Nom de la catégorie" required />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-3" placeholder="Description de la catégorie"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                  <button type="button" id="cancelCategoryModal" class="px-4 py-2 rounded-lg border">Annuler</button>
                  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-5 py-2.5">✅ Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function(){
            const openBtn = document.getElementById('openCategoryModal');
            const modal = document.getElementById('categoryModal');
            const closeBtn = document.getElementById('closeCategoryModal');
            const cancelBtn = document.getElementById('cancelCategoryModal');
            const form = document.getElementById('categoryForm');
            const backdrop = document.getElementById('modalBackdrop');

            function openModal(){ 
                modal.classList.remove('hidden'); 
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            
            function closeModal(){ 
                modal.classList.add('hidden'); 
                modal.style.display = 'none';
                document.body.style.overflow = '';
                form.reset();
            }

            // Event listeners
            if (openBtn) {
                openBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal();
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal();
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeModal();
                });
            }

            // Fermer en cliquant sur le backdrop
            if (backdrop) {
                backdrop.addEventListener('click', function(e) {
                    if (e.target === backdrop) {
                        closeModal();
                    }
                });
            }

            // Fermer avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            // Gestion du formulaire
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const original = submitBtn.textContent;
                    submitBtn.textContent = '⏳ Enregistrement…';
                    submitBtn.disabled = true;
                    
                    // Le formulaire se soumet normalement
                    // La page se rechargera avec le message de succès
                });
            }
          });
        </script>
    </div>
@endsection
