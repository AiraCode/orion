<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RDController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard & Simulation
    Route::get('/dashboard', [GameController::class, 'dashboard'])->name('dashboard');
    Route::post('/api/simulation/advance', [GameController::class, 'advanceTime'])->name('simulation.advance');
    Route::post('/api/simulation/speed', [GameController::class, 'setSpeed'])->name('simulation.speed');

    // Domain Modules
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('/procurement/order', [ProcurementController::class, 'order'])->name('procurement.order');

    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::post('/production/produce', [ProductionController::class, 'produce'])->name('production.produce');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees/hire', [EmployeeController::class, 'hire'])->name('employees.hire');

    Route::get('/market', [MarketController::class, 'index'])->name('market.index');
    Route::get('/rd', [RDController::class, 'index'])->name('rd.index');
    Route::post('/rd/store', [RDController::class, 'store'])->name('rd.store');

    Route::get('/investors', [InvestorController::class, 'index'])->name('investors.index');
    Route::get('/notifications', [AlertController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [AlertController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

