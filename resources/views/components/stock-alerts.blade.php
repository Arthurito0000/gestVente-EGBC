@if(isset($globalStockAlerts) && (count($globalStockAlerts['ruptures']) > 0 || count($globalStockAlerts['alertes']) > 0))
<div class="fixed top-4 right-4 z-50 max-w-sm" id="stockAlertsContainer">
    @if(count($globalStockAlerts['ruptures']) > 0)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2 shadow-lg stock-alert" role="alert" id="ruptureAlert">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-lg mr-2">🚨</div>
                <div>
                    <strong class="font-bold">Rupture de stock!</strong>
                    <span class="block sm:inline">{{ count($globalStockAlerts['ruptures']) }} produit(s) en rupture</span>
                </div>
            </div>
            <button onclick="closeAlert('ruptureAlert')" class="ml-2 text-red-500 hover:text-red-700 font-bold text-lg leading-none">&times;</button>
        </div>
        <div class="mt-2">
            @foreach($globalStockAlerts['ruptures']->take(3) as $rupture)
            <div class="text-xs">• {{ $rupture->product->sku ?? 'N/A' }} - {{ $rupture->product->nom ?? 'N/A' }}</div>
            @endforeach
            @if(count($globalStockAlerts['ruptures']) > 3)
            <div class="text-xs font-semibold">... et {{ count($globalStockAlerts['ruptures']) - 3 }} autre(s)</div>
            @endif
        </div>
        @can('view-stock')
        <div class="mt-2">
            <a href="{{ route('stock.index') }}" class="text-xs underline hover:text-red-900">Voir les stocks →</a>
        </div>
        @endcan
    </div>
    @endif

    @if(count($globalStockAlerts['alertes']) > 0)
    <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded mb-2 shadow-lg stock-alert" role="alert" id="alerteAlert">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-lg mr-2">⚠️</div>
                <div>
                    <strong class="font-bold">Stock faible!</strong>
                    <span class="block sm:inline">{{ count($globalStockAlerts['alertes']) }} produit(s) en alerte</span>
                </div>
            </div>
            <button onclick="closeAlert('alerteAlert')" class="ml-2 text-orange-500 hover:text-orange-700 font-bold text-lg leading-none">&times;</button>
        </div>
        <div class="mt-2">
            @foreach($globalStockAlerts['alertes']->take(3) as $alerte)
            <div class="text-xs">• {{ $alerte->product->sku ?? 'N/A' }} ({{ $alerte->quantite }}/{{ $alerte->seuil }})</div>
            @endforeach
            @if(count($globalStockAlerts['alertes']) > 3)
            <div class="text-xs font-semibold">... et {{ count($globalStockAlerts['alertes']) - 3 }} autre(s)</div>
            @endif
        </div>
        @can('view-stock')
        <div class="mt-2">
            <a href="{{ route('stock.index') }}" class="text-xs underline hover:text-orange-900">Voir les stocks →</a>
        </div>
        @endcan
    </div>
    @endif
</div>

<script>
// Fonction pour fermer une alerte spécifique
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        
        setTimeout(() => {
            alert.remove();
            
            // Si plus d'alertes, masquer le conteneur
            const container = document.getElementById('stockAlertsContainer');
            if (container && container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }
    
    // Stocker en localStorage pour éviter de réafficher pendant cette session
    localStorage.setItem('stockAlert_' + alertId + '_dismissed', Date.now());
}

// Auto-disparition après 10 secondes (seulement pour les alertes stock faible, pas les ruptures)
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si les alertes ont déjà été fermées dans cette session
    const ruptureAlert = document.getElementById('ruptureAlert');
    const alerteAlert = document.getElementById('alerteAlert');
    
    if (ruptureAlert && localStorage.getItem('stockAlert_ruptureAlert_dismissed')) {
        const dismissTime = parseInt(localStorage.getItem('stockAlert_ruptureAlert_dismissed'));
        // Si fermé il y a moins de 30 minutes, ne pas réafficher
        if (Date.now() - dismissTime < 30 * 60 * 1000) {
            ruptureAlert.remove();
        }
    }
    
    if (alerteAlert && localStorage.getItem('stockAlert_alerteAlert_dismissed')) {
        const dismissTime = parseInt(localStorage.getItem('stockAlert_alerteAlert_dismissed'));
        // Si fermé il y a moins de 30 minutes, ne pas réafficher
        if (Date.now() - dismissTime < 30 * 60 * 1000) {
            alerteAlert.remove();
        }
    }
    
    // Auto-fermeture pour les alertes stock faible après 15 secondes
    if (alerteAlert && !localStorage.getItem('stockAlert_alerteAlert_dismissed')) {
        setTimeout(() => {
            if (document.getElementById('alerteAlert')) {
                closeAlert('alerteAlert');
            }
        }, 15000);
    }
    
    // Animation d'entrée
    const alerts = document.querySelectorAll('.stock-alert');
    alerts.forEach((alert, index) => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        alert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        
        setTimeout(() => {
            alert.style.opacity = '1';
            alert.style.transform = 'translateX(0)';
        }, index * 200);
    });
});
</script>
@endif
