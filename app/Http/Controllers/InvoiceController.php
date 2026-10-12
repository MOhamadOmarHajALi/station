<?php

namespace App\Http\Controllers;

use App\Models\{Customer, Invoice, Photographer};
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InvoiceController extends Controller
{
    private function type(Request $r): string
    {
        return $r->query('type') === 'expense' ? 'expense' : 'income';
    }

    public function index(Request $r)
    {
        $type = $this->type($r);

        return Inertia::render('Invoices/Index', [
            'type' => $type,
            'grand' => (float) Invoice::ofType($type)->sum('total'),
            'invoices' => Invoice::with(['customer:id,name', 'photographer:id,name'])->ofType($type)
                ->latest('invoice_date')->latest('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function create(Request $r)
    {
        $type = $this->type($r);

        return Inertia::render('Invoices/Create', [
            'type' => $type,
            'number' => Invoice::nextNumber($type),
            'today' => now()->toDateString(),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'photographers' => Photographer::orderBy('name')->get(['id', 'name', 'default_rate']),
            'selectedPhotographer' => $r->query('photographer') ? (int) $r->query('photographer') : null,
        ]);
    }

    public function store(Request $r, InvoiceService $service)
    {
        $data = $r->validate([
            'type' => 'required|in:income,expense',
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|required_if:type,income|exists:customers,id',
            'photographer_id' => 'nullable|exists:photographers,id',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ], [
            'items.*.description.required' => 'اكتب وصف البند',
            'items.*.price.required' => 'أدخل السعر',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'customer_id.required_if' => 'اختر الزبون',
        ]);

        try {
            $invoice = $service->create([
                'type' => $data['type'],
                'invoice_date' => $data['invoice_date'],
                'customer_id' => $data['type'] === 'income' ? $data['customer_id'] : null,
                'photographer_id' => $data['type'] === 'expense' ? ($data['photographer_id'] ?? null) : null,
                'title' => $data['title'] ?? null,
                'notes' => $data['notes'] ?? null,
            ], $data['items']);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'تعذّر حفظ الفاتورة، لم يتم حفظ أي شيء. حاول مرة أخرى.');
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'تم إنشاء الفاتورة '.$invoice->number);
    }

    public function show(Invoice $invoice)
    {
        return Inertia::render('Invoices/Show', ['invoice' => $invoice->load(['items', 'customer:id,name', 'photographer:id,name'])]);
    }

    public function destroy(Invoice $invoice)
    {
        $type = $invoice->type;
        $invoice->delete();
        return redirect()->route('invoices.index', ['type' => $type])->with('success', 'تم حذف الفاتورة');
    }
}
