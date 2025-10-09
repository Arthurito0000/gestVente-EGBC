import './bootstrap';
import Alpine from 'alpinejs'
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Import mobile CSS
import '../css/mobile.css';

window.Alpine = Alpine
Alpine.start()


// Configuration Toastr
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-top-right",
    preventDuplicates: false,
    showDuration: 300,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};

// Rendre toastr disponible globalement
window.toastr = toastr;

console.log('Toastr chargé:', typeof toastr); // Pour debug

// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.getElementById('sidebar');
    
    if (mobileMenuButton && sidebar) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (sidebar && !sidebar.classList.contains('hidden') && 
            !sidebar.contains(event.target) && 
            mobileMenuButton && !mobileMenuButton.contains(event.target)) {
            sidebar.classList.add('hidden');
        }
    });
});