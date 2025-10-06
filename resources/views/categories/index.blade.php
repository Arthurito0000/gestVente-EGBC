@extends('layouts.app')
@section('title', 'Gestions des categories')

@section('content')
    <div class="">
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

        <!-- Modal: Ajouter/Modifier Catégorie -->
        <div id="categoryModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 99998;">
          <div class="absolute inset-0 bg-black/50" id="modalBackdrop"></div>
          <div class="relative w-full max-w-lg p-4" style="z-index: 99999;">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
              <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 id="modalTitle" class="font-heading text-lg text-gray-900">Ajouter une catégorie</h3>
                <button id="closeCategoryModal" class="p-2 rounded hover:bg-gray-100" aria-label="Fermer">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
              </div>
              <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" class="px-6 py-5 space-y-5">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="category_id" id="categoryId">
                
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                  <input id="name" name="name" type="text" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-2.5" placeholder="Nom de la catégorie" required />
                </div>
                
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                  <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-gray-300 bg-gray-50 hover:bg-white focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors px-3 py-2.5" placeholder="Description de la catégorie"></textarea>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-2">
                  <button type="button" id="cancelCategoryModal" class="px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                    Annuler
                  </button>
                  <button type="submit" id="submitButton" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-5 py-2.5 transition-colors">
                    <span class="flex items-center justify-center">
                      <svg id="submitSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <span id="submitText">Enregistrer</span>
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function(){
            // Éléments du DOM
            const openBtn = document.getElementById('openCategoryModal');
            const modal = document.getElementById('categoryModal');
            const closeBtn = document.getElementById('closeCategoryModal');
            const cancelBtn = document.getElementById('cancelCategoryModal');
            const form = document.getElementById('categoryForm');
            const backdrop = document.getElementById('modalBackdrop');
            const modalTitle = document.getElementById('modalTitle');
            const formMethod = document.getElementById('formMethod');
            const categoryId = document.getElementById('categoryId');
            const nameInput = document.getElementById('name');
            const descriptionInput = document.getElementById('description');
            const submitButton = document.getElementById('submitButton');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            // Fonctions utilitaires
            function openModal(mode = 'create', category = null) { 
                // Réinitialiser le formulaire
                form.reset();
                formMethod.value = 'POST';
                form.action = '{{ route("categories.store") }}';
                
                if (mode === 'edit' && category) {
                    // Mode édition
                    modalTitle.textContent = 'Modifier la catégorie';
                    formMethod.value = 'PUT';
                    form.action = '{{ url("categories") }}/' + category.id;
                    categoryId.value = category.id;
                    nameInput.value = category.name || '';
                    descriptionInput.value = category.description || '';
                } else {
                    // Mode création
                    modalTitle.textContent = 'Ajouter une catégorie';
                    formMethod.value = 'POST';
                    form.action = '{{ route("categories.store") }}';
                    categoryId.value = '';
                }
                
                // Afficher le modal
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                // Focus sur le premier champ
                setTimeout(() => nameInput.focus(), 100);
            }
            
            function closeModal() { 
                modal.classList.add('hidden'); 
                modal.style.display = 'none';
                document.body.style.overflow = '';
                form.reset();
            }

            function setFormLoading(isLoading) {
                if (isLoading) {
                    submitButton.disabled = true;
                    submitSpinner.classList.remove('hidden');
                    submitText.textContent = 'Traitement...';
                } else {
                    submitButton.disabled = false;
                    submitSpinner.classList.add('hidden');
                    submitText.textContent = 'Enregistrer';
                }
            }

            // Événements
            // Ouvrir le modal en mode création
            if (openBtn) {
                openBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal('create');
                });
            }
            
            // Gérer les boutons d'édition
            document.querySelectorAll('.edit-category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryData = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        description: this.getAttribute('data-description')
                    };
                    openModal('edit', categoryData);
                });
            });
            
            // Fermer le modal
            function setupCloseHandlers() {
                if (closeBtn) {
                    closeBtn.addEventListener('click', closeModal);
                }
                
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', closeModal);
                }
                
                if (backdrop) {
                    backdrop.addEventListener('click', function(e) {
                        if (e.target === backdrop) {
                            closeModal();
                        }
                    });
                }
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            }
            setupCloseHandlers();

            // Form submission handler
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Disable the button and show spinner
                    setFormLoading(true);
                    
                    // Get the form data
                    const formData = new FormData(form);
                    const method = formData.get('_method') || 'POST';
                    const isPutOrPatch = method === 'PUT' || method === 'PATCH';
                    
                    // Set up the request options
                    const options = {
                        method: isPutOrPatch ? 'POST' : method,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    };
                    
                    // If this is an update, add the _method parameter
                    if (isPutOrPatch) {
                        formData.append('_method', method);
                    }
                    
                    // Determine the URL based on the form action
                    let url = form.action;
                    
                    try {
                        const response = await fetch(url, options);
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Une erreur est survenue lors de la requête');
                        }
                        
                        // Show success message and reload the page
                        if (data.success) {
                            // Show success modal
                            showSuccessModal('Succès', data.message || 'Opération effectuée avec succès');
                            
                            // Close the modal and refresh the page after a short delay
                            setTimeout(() => {
                                if (modal) {
                                    closeModal();
                                }
                                window.location.reload();
                            }, 1800);
                        } else {
                            throw new Error(data.message || 'Une erreur est survenue');
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        
                        // Show error message
                        if (typeof showNotification === 'function') {
                            showNotification('Erreur', error.message || 'Une erreur est survenue', 'error');
                        } else {
                            alert('Erreur: ' + (error.message || 'Une erreur est survenue lors de la soumission du formulaire'));
                        }
                        
                        // Re-enable the form
                        setFormLoading(false);
                    }
                });
            }
            
            // Gérer la touche Entrée pour soumettre le formulaire
            if (nameInput) {
                nameInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        form.dispatchEvent(new Event('submit'));
                    }
                });
            }
          });
        </script>

        <!-- Modal de confirmation de suppression -->
        <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm transition-all duration-300">
          <div class="flex items-center justify-center min-h-screen px-4">
            <div id="modalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
              <!-- Bouton de fermeture -->
              <button id="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
              
              <div class="p-8 text-center">
                <!-- Icône d'alerte animée -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6 animate-pulse">
                  <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                </div>
                
                <!-- Titre -->
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Confirmer la suppression</h3>
                
                <!-- Message -->
                <div class="mb-8">
                  <p class="text-gray-600 mb-2">
                    Êtes-vous sûr de vouloir supprimer la catégorie
                  </p>
                  <p class="font-semibold text-gray-900 text-lg" id="categoryName"></p>
                  <p class="text-sm text-red-600 mt-3 bg-red-50 px-4 py-2 rounded-lg">
                    ⚠️ Cette action est irréversible
                  </p>
                </div>
                
                <!-- Boutons -->
                <div class="flex gap-3">
                  <button id="cancelDelete" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 hover:scale-105">
                    Annuler
                  </button>
                  <button id="confirmDelete" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 hover:scale-105 shadow-lg">
                    Supprimer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du modal de suppression
            let deleteForm = null;
            
            // Fonction pour afficher le modal de suppression
            function showDeleteModal(form, name) {
                deleteForm = form;
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('modalContent');
                const categoryName = document.getElementById('categoryName');
                
                // Mettre à jour le nom de la catégorie dans le message
                categoryName.textContent = name;
                
                // Afficher le modal avec animation
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Empêcher le défilement du body
                document.body.style.overflow = 'hidden';
            }
            
            // Fonction pour cacher le modal de suppression
            function hideDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('modalContent');
                
                // Animation de fermeture
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                
                // Cacher le modal après l'animation
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 200);
            }
            
            // Gestionnaire d'événement pour le bouton de suppression
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const name = this.getAttribute('data-product-name');
                    showDeleteModal(form, name);
                });
            });
            
            // Gestionnaire pour le bouton d'annulation
            document.getElementById('cancelDelete')?.addEventListener('click', hideDeleteModal);
            
            // Gestionnaire pour le bouton de fermeture
            document.getElementById('closeModal')?.addEventListener('click', hideDeleteModal);
            
            // Gestionnaire pour le bouton de confirmation de suppression
            document.getElementById('confirmDelete')?.addEventListener('click', function() {
                if (deleteForm) {
                    // Désactiver le bouton de confirmation
                    this.disabled = true;
                    this.innerHTML = 'Suppression en cours...';
                    
                    // Soumettre le formulaire
                    deleteForm.submit();
                }
            });
            
            // Fermer le modal en cliquant en dehors
            document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });
            
            // Fermer le modal avec la touche Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !document.getElementById('deleteModal')?.classList.contains('hidden')) {
                    hideDeleteModal();
                }
            });
        });
        </script>
    </div>
        <!-- Success Modal -->
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Title and Message -->
                    <h3 id="successTitle" class="text-lg font-medium text-gray-900 mb-2">Succès</h3>
                    <p id="successMessage" class="text-gray-600 mb-6">Opération effectuée avec succès</p>
                    
                    <!-- Button -->
                    <button id="closeSuccessModal" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
        
        <style>
            /* Animation for success modal */
            @keyframes modalFadeIn {
                from { opacity: 0; transform: translate(-50%, -48%) scale(0.95); }
                to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            }
            
            .modal-animate {
                animation: modalFadeIn 0.15s ease-out forwards;
            }
        </style>
        
        <script>
            // Success modal functionality
            const successModal = document.getElementById('successModal');
            const closeSuccessModal = document.getElementById('closeSuccessModal');
            let modalContent = null;
            
            // Initialize modal content once DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                modalContent = successModal.querySelector('.relative');
            });
            
            function showSuccessModal(title, message) {
                const titleEl = document.getElementById('successTitle');
                const messageEl = document.getElementById('successMessage');
                
                // Set title and message
                if (titleEl) titleEl.textContent = title || 'Succès';
                if (messageEl) messageEl.textContent = message || 'Opération effectuée avec succès';
                
                // Show modal with animation if modalContent is found
                if (successModal && modalContent) {
                    successModal.classList.remove('hidden');
                    modalContent.classList.add('modal-animate');
                    document.body.style.overflow = 'hidden';
                    
                    // Auto-close after 3 seconds
                    setTimeout(() => {
                        if (closeSuccessModal) closeSuccessModal.click();
                    }, 3000);
                } else {
                    // Fallback to alert if modal elements not found
                    alert(title + ': ' + (message || 'Opération effectuée avec succès'));
                }
            }
            
            // Close modal handler
            if (closeSuccessModal && modalContent) {
                closeSuccessModal.addEventListener('click', () => {
                    if (!modalContent) return;
                    
                    modalContent.classList.remove('modal-animate');
                    modalContent.style.opacity = '0';
                    modalContent.style.transform = 'translate(-50%, -48%) scale(0.95)';
                    
                    setTimeout(() => {
                        if (successModal) {
                            successModal.classList.add('hidden');
                            document.body.style.overflow = '';
                            modalContent.style.opacity = '';
                            modalContent.style.transform = '';
                        }
                    }, 150);
                });
            }
            
            // Close when clicking on backdrop
            if (successModal) {
                successModal.addEventListener('click', (e) => {
                    if (e.target === successModal && closeSuccessModal) {
                        closeSuccessModal.click();
                    }
                });
            }
            
            // Make function available globally
            window.showSuccessModal = showSuccessModal;
        </script>
@endsection
