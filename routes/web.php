<?php

use App\Http\Controllers\{ContactController, CustomerController, DashboardController, InvoiceController, LandingController, MessageController, PhotographerController, PortfolioController, RecurringInvoiceController, ReportController};
use App\Http\Middleware\EnsureAllowedAdmin;
use Illuminate\Support\Facades\Route;

// ===== عام (الزوار) =====
Route::get('/', [LandingController::class, 'show'])->name('landing');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('media/portfolio/{item}', [PortfolioController::class, 'image'])->name('portfolio.image');

// التسجيل مُلغى (هذا المسار يسبق مسارات Breeze فيُغلقها)
Route::any('register', [LandingController::class, 'closed']);

// ===== النظام: المدراء الثلاثة فقط =====
Route::middleware(['auth', EnsureAllowedAdmin::class])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('photographers', PhotographerController::class)->except('show');
    Route::resource('invoices', InvoiceController::class);

    Route::post('recurring/run', [RecurringInvoiceController::class, 'run'])->name('recurring.run');
    Route::resource('recurring', RecurringInvoiceController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');

    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::patch('messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // صور صفحة الموقع
    Route::get('portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::match(['put', 'patch'], 'portfolio/{item}', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::post('portfolio/{item}/move', [PortfolioController::class, 'move'])->name('portfolio.move');
    Route::delete('portfolio/{item}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
});

require __DIR__.'/auth.php';
