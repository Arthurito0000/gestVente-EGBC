<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// Redirection root vers login
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// ============================================
// AUTH ROUTES - SANS MIDDLEWARE GUEST
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Réinitialisation mot de passe
Route::get('/email/verify', [AuthController::class, 'showEmailVerificationForm'])->name('verification.notice');
Route::post('/email/verify', [AuthController::class, 'sendVerificationEmail'])->name('verification.send');
Route::get('password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// ============================================
// ROUTES PROTÉGÉES (Authentification requise)
// ============================================

Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view-dashboard');

    // INVENTAIRE - PRODUITS
    Route::prefix('products')->name('products.')->middleware('permission:view-products')->group(function () {
        Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ProductController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-products');
        Route::post('/', [App\Http\Controllers\ProductController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-products');
        Route::get('/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-products');
        Route::put('/{product}', [App\Http\Controllers\ProductController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-products');
        Route::delete('/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-products');
    });

    // INVENTAIRE - STOCK
    Route::prefix('stock')->name('stock.')->middleware('permission:view-stock')->group(function () {
        Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index');
        Route::get('/{product}', [App\Http\Controllers\StockController::class, 'show'])->name('show');
        Route::delete('/{stock}', [App\Http\Controllers\StockController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:manage-stock');
    });

    // INVENTAIRE - MOUVEMENTS
    Route::prefix('movements')->name('movements.')->middleware('permission:view-movements')->group(function () {
        Route::get('/', [App\Http\Controllers\MovementController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\MovementController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-movements');
        Route::post('/', [App\Http\Controllers\MovementController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-movements');
    });

    // EXPORTS
    Route::prefix('export')->name('export.')->group(function () {
        Route::middleware('permission:export-products')->group(function () {
            Route::get('/products/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.excel');
            Route::get('/products/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.pdf');
            Route::get('/products/preview', [App\Http\Controllers\Export\ExportController::class, 'productsPreview'])->name('products.preview');
        });
        
        Route::get('/products/stats', [App\Http\Controllers\Export\ExportController::class, 'productsStats'])
            ->name('products.stats')
            ->middleware('permission:view-products');
        
        Route::middleware('permission:export-stock')->group(function () {
            Route::get('/stocks/excel', [App\Http\Controllers\Export\ExportController::class, 'stocksExcel'])->name('stocks.excel');
            Route::get('/stocks/pdf', [App\Http\Controllers\Export\ExportController::class, 'stocksPdf'])->name('stocks.pdf');
            Route::get('/stocks/preview', [App\Http\Controllers\Export\ExportController::class, 'stocksPreview'])->name('stocks.preview');
            Route::get('/stocks/view', [App\Http\Controllers\Export\ExportController::class, 'stocksView'])->name('stocks.view');
            Route::get('/stocks/inventaire', [App\Http\Controllers\Export\ExportController::class, 'stocksInventaire'])->name('stocks.inventaire');
            Route::get('/stocks/inventaire/preview', [App\Http\Controllers\Export\ExportController::class, 'stocksInventairePreview'])->name('stocks.inventaire.preview');
        });
        
        Route::get('/stocks/test', [App\Http\Controllers\Export\ExportController::class, 'stocksTest'])->name('stocks.test');
        Route::get('/stocks/stats', [App\Http\Controllers\Export\ExportController::class, 'stocksStats'])
            ->name('stocks.stats')
            ->middleware('permission:view-stock');
        
        Route::middleware('permission:export-reports')->group(function () {
            Route::get('/movements/excel', [App\Http\Controllers\Export\ExportController::class, 'movementsExcel'])->name('movements.excel');
            Route::get('/movements/pdf', [App\Http\Controllers\Export\ExportController::class, 'movementsPdf'])->name('movements.pdf');
        });
    });

    Route::get('/products/export/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.export.excel');
    Route::get('/products/export/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.export.pdf');

    // NOTIFICATIONS
    Route::prefix('notifications')->name('notifications.')->middleware('permission:receive-stock-alerts')->group(function () {
        Route::get('/stock', [App\Http\Controllers\NotificationController::class, 'getStockNotifications'])->name('stock');
        Route::get('/count', [App\Http\Controllers\NotificationController::class, 'getNotificationCount'])->name('count');
        Route::get('/stats', [App\Http\Controllers\NotificationController::class, 'getNotificationStats'])->name('stats');
        Route::post('/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    });

    // CATÉGORIES
   // CATÉGORIES
Route::resource('categories', CategoryController::class)->except(['create', 'edit']);

    // VENTES
    Route::prefix('sales')->name('sales.')->middleware('permission:view-sales')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-sales');
        Route::post('/', [SaleController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-sales');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-sales');
        Route::put('/{sale}', [SaleController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-sales');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-sales');
    });

    Route::get('api/products/{product}/stock', [SaleController::class, 'checkStock'])
        ->name('api.products.stock')
        ->middleware('permission:view-sales');

    // FACTURES
    Route::prefix('invoices')->name('invoices.')->middleware('permission:manage-invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])
            ->name('print')
            ->middleware('permission:view-sales');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])
            ->name('download')
            ->middleware('permission:view-sales');
    });

    // DEVIS
    Route::prefix('quotes')->name('quotes.')->middleware('permission:manage-quotes')->group(function () {
        Route::get('/', [App\Http\Controllers\QuoteController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\QuoteController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\QuoteController::class, 'store'])->name('store');
        Route::get('/{quote}', [App\Http\Controllers\QuoteController::class, 'show'])->name('show');
        Route::get('/{quote}/edit', [App\Http\Controllers\QuoteController::class, 'edit'])->name('edit');
        Route::put('/{quote}', [App\Http\Controllers\QuoteController::class, 'update'])->name('update');
        Route::delete('/{quote}', [App\Http\Controllers\QuoteController::class, 'destroy'])->name('destroy');
        Route::get('/{quote}/print', [App\Http\Controllers\QuoteController::class, 'print'])->name('print');
        Route::post('/{quote}/download-pdf', [App\Http\Controllers\QuoteController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('/{quote}/convert', [App\Http\Controllers\QuoteController::class, 'convertToSale'])->name('convert');
        Route::post('/{quote}/duplicate', [App\Http\Controllers\QuoteController::class, 'duplicate'])->name('duplicate');
    });

    // ADMINISTRATION - UTILISATEURS
    Route::prefix('users')->name('users.')->middleware('permission:manage-users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/emergency-reset', [UserManagementController::class, 'showEmergencyReset'])->name('emergency-reset');
        Route::post('/emergency-reset/password', [UserManagementController::class, 'emergencyPasswordReset'])->name('emergency-reset.password');
        Route::post('/emergency-reset/link', [UserManagementController::class, 'sendEmergencyResetLink'])->name('emergency-reset.link');
        Route::get('/security-stats', [UserManagementController::class, 'getSecurityStats'])->name('security-stats');
    });

    // RÔLES & PERMISSIONS
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('/users/roles/management', [UserManagementController::class, 'roles'])->name('users.roles');
        Route::middleware('permission:manage-permissions')->group(function () {
            Route::get('/users/roles/{role}/permissions', [UserManagementController::class, 'getRolePermissions'])->name('users.roles.permissions');
            Route::put('/users/roles/{role}/permissions', [UserManagementController::class, 'updateRolePermissions'])->name('users.roles.update');
        });
    });
});