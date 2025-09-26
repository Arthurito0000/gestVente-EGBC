<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserManagementController;
// use App\Http\Controllers\InvoiceController; // duplicate removed
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

// Redirect root to login for now (frontend only)
Route::get('/', function () { return redirect()->route('login'); });

// Auth (frontend-only)
Route::get('/login', function () { return view('auth.login'); })->name('login');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Request reset link (email form)
Route::get('/email/verify', [AuthController::class, 'showEmailVerificationForm'])->name('verification.notice');
Route::post('/email/verify', [AuthController::class, 'sendVerificationEmail'])->name('verification.send');

// Reset password via secure token link
Route::get('password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Protected routes
Route::middleware('auth')->group(function () {
    
// Dashboard
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard')->middleware(['auth', 'permission:view-dashboard']);

// Inventory - Produits avec permissions
Route::middleware(['auth'])->group(function () {
    Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index')->middleware('permission:view-products');
    Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create')->middleware('permission:create-products');
});

// Export routes avec permissions
Route::prefix('export')->name('export.')->middleware(['auth'])->group(function () {
    // Exports produits
    Route::get('/products/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.excel')->middleware('permission:export-products');
    Route::get('/products/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.pdf')->middleware('permission:export-products');
    Route::get('/products/preview', [App\Http\Controllers\Export\ExportController::class, 'productsPreview'])->name('products.preview')->middleware('permission:export-products');
    Route::get('/products/stats', [App\Http\Controllers\Export\ExportController::class, 'productsStats'])->name('products.stats')->middleware('permission:view-products');
    
    // Exports stocks
    Route::get('/stocks/excel', [App\Http\Controllers\Export\ExportController::class, 'stocksExcel'])->name('stocks.excel')->middleware('permission:export-stock');
    Route::get('/stocks/pdf', [App\Http\Controllers\Export\ExportController::class, 'stocksPdf'])->name('stocks.pdf')->middleware('permission:export-stock');
    Route::get('/stocks/preview', [App\Http\Controllers\Export\ExportController::class, 'stocksPreview'])->name('stocks.preview')->middleware('permission:export-stock');
    Route::get('/stocks/view', [App\Http\Controllers\Export\ExportController::class, 'stocksView'])->name('stocks.view')->middleware('permission:export-stock');
    Route::get('/stocks/test', [App\Http\Controllers\Export\ExportController::class, 'stocksTest'])->name('stocks.test');
    Route::get('/stocks/stats', [App\Http\Controllers\Export\ExportController::class, 'stocksStats'])->name('stocks.stats')->middleware('permission:view-stock');
    Route::get('/stocks/inventaire', [App\Http\Controllers\Export\ExportController::class, 'stocksInventaire'])->name('stocks.inventaire')->middleware('permission:export-stock');
    Route::get('/stocks/inventaire/preview', [App\Http\Controllers\Export\ExportController::class, 'stocksInventairePreview'])->name('stocks.inventaire.preview')->middleware('permission:export-stock');
    
    // Exports mouvements
    Route::get('/movements/excel', [App\Http\Controllers\Export\ExportController::class, 'movementsExcel'])->name('movements.excel')->middleware('permission:export-reports');
    Route::get('/movements/pdf', [App\Http\Controllers\Export\ExportController::class, 'movementsPdf'])->name('movements.pdf')->middleware('permission:export-reports');
});


// Routes de compatibilité (anciennes URLs)
Route::get('/products/export/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.export.excel');
Route::get('/products/export/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.export.pdf');

// Routes produits avec permissions détaillées
Route::middleware(['auth'])->group(function () {
    Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store')->middleware('permission:create-products');
    Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show')->middleware('permission:view-products');
    Route::get('/products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit')->middleware('permission:edit-products');
    Route::put('/products/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update')->middleware('permission:edit-products');
    Route::delete('/products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:delete-products');
});

// Routes stock avec permissions
Route::prefix('stock')->name('stock.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index')->middleware('permission:view-stock');
    Route::get('/{product}', [App\Http\Controllers\StockController::class, 'show'])->name('stock.show')->middleware('permission:view-stock');
    Route::delete('/{stock}', [App\Http\Controllers\StockController::class, 'destroy'])->name('destroy')->middleware('permission:manage-stock');
});

// Routes mouvements avec permissions
Route::prefix('movements')->name('movements.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\MovementController::class, 'index'])->name('index')->middleware('permission:view-movements');
    Route::get('/create', [App\Http\Controllers\MovementController::class, 'create'])->name('create')->middleware('permission:create-movements');
    Route::post('/', [App\Http\Controllers\MovementController::class, 'store'])->name('store')->middleware('permission:create-movements');
});

// Notifications avec permissions
Route::prefix('notifications')->name('notifications.')->middleware(['auth'])->group(function () {
    Route::get('/stock', [App\Http\Controllers\NotificationController::class, 'getStockNotifications'])->name('stock')->middleware('permission:receive-stock-alerts');
    Route::get('/count', [App\Http\Controllers\NotificationController::class, 'getNotificationCount'])->name('count')->middleware('permission:receive-stock-alerts');
    Route::get('/stats', [App\Http\Controllers\NotificationController::class, 'getNotificationStats'])->name('stats')->middleware('permission:receive-stock-alerts');
    Route::post('/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read')->middleware('permission:receive-stock-alerts');
});

// Sales

// Administration - Gestion des utilisateurs et rôles
Route::middleware(['auth'])->group(function () {
    // Routes pour la gestion des utilisateurs (avec permissions détaillées)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index')->middleware('permission:manage-users');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create')->middleware('permission:manage-users');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store')->middleware('permission:manage-users');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show')->middleware('permission:manage-users');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit')->middleware('permission:manage-users');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update')->middleware('permission:manage-users');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('permission:manage-users');
    
    // Routes spéciales pour les rôles et permissions
    Route::get('/users/roles/management', [UserManagementController::class, 'roles'])->name('users.roles')->middleware('permission:manage-roles');
    Route::get('/users/roles/{role}/permissions', [UserManagementController::class, 'getRolePermissions'])->name('users.roles.permissions')->middleware('permission:manage-permissions');
    Route::put('/users/roles/{role}/permissions', [UserManagementController::class, 'updateRolePermissions'])->name('users.roles.update')->middleware('permission:manage-permissions');
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:manage-users');
});

//Categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
});

// Invoices - Réservées aux Vendeurs et Administrateurs
Route::middleware(['auth'])->group(function () {
    Route::resource('invoices', InvoiceController::class)->middleware('permission:manage-invoices');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print')->middleware('permission:view-sales');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download')->middleware('permission:view-sales');
});

});