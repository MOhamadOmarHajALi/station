<?php

use App\Http\Controllers\{ContactController, MessageController, CustomerController, DashboardController, InvoiceController, PhotographerController, RecurringInvoiceController, ReportController, UserController};
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : Inertia::render('Landing'))->name('landing');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::middleware('auth')->group(function () {

    // ✍️ الكتابة: مدير أو محاسب فقط (يجب أن تأتي قبل مسارات {id})
    Route::middleware('can:manage')->group(function () {
        Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
        Route::patch('messages/{message}', [MessageController::class, 'update'])->name('messages.update');
        Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::resource('customers', CustomerController::class)->except(['index', 'show']);
        Route::resource('photographers', PhotographerController::class)->except(['index', 'show']);
        Route::resource('invoices', InvoiceController::class)->only(['create', 'store', 'destroy']);
        Route::post('recurring/run', [RecurringInvoiceController::class, 'run'])->name('recurring.run');
        Route::resource('recurring', RecurringInvoiceController::class)->only(['store', 'update', 'destroy']);
    });

    // 🔐 إدارة المستخدمين: مدير فقط
    Route::middleware('can:admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    // 👁️ القراءة: أي مستخدم مسجّل
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('photographers', [PhotographerController::class, 'index'])->name('photographers.index');
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('recurring', [RecurringInvoiceController::class, 'index'])->name('recurring.index');
    Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
});

require __DIR__.'/auth.php';
