<?php

namespace App\Http\Controllers;

use App\Models\{Customer, Photographer, RecurringInvoice};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Recurring/Index', [
            'items' => RecurringInvoice::with(['customer:id,name', 'photographer:id,name'])->latest()->get(),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'photographers' => Photographer::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'day_of_month' => 'required|integer|between:1,31',
            'customer_id' => 'nullable|required_if:type,income|exists:customers,id',
            'photographer_id' => 'nullable|exists:photographers,id',
        ], ['customer_id.required_if' => 'اختر الزبون لفاتورة الوارد']);

        RecurringInvoice::create($data + ['active' => true]);
        return back()->with('success', 'تمت إضافة الفاتورة المتكررة');
    }

    public function update(Request $r, RecurringInvoice $recurring)
    {
        $recurring->update($r->validate(['active' => 'required|boolean']));
        return back()->with('success', $recurring->active ? 'تم التفعيل' : 'تم الإيقاف');
    }

    public function destroy(RecurringInvoice $recurring)
    {
        $recurring->delete();
        return back()->with('success', 'تم الحذف');
    }

    /** تشغيل الأتمتة يدويًا الآن */
    public function run()
    {
        Artisan::call('station:run-recurring');
        return back()->with('success', trim(Artisan::output()) ?: 'تم التشغيل');
    }
}
