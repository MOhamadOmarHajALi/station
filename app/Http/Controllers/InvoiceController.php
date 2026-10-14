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

    private function rules(): array
    {
        return [
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|exists:customers,id',
            'photographer_id' => 'nullable|exists:photographers,id',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    private function messages(): array
    {
        return [
            'items.*.description.required' => 'اكتب وصف البند',
            'items.*.price.required' => 'أدخل السعر',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'customer_id.required' => 'اختر الزبون',
        ];
    }

    private function lookups(): array
    {
        return [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'photographers' => Photographer::orderBy('name')->get(['id', 'name', 'default_rate']),
        ];
    }

    private function partyFields(string $type, array $data): array
    {
        return [
            'invoice_date' => $data['invoice_date'],
            'customer_id' => $type === 'income' ? $data['customer_id'] : null,
            'photographer_id' => $type === 'expense' ? ($data['photographer_id'] ?? null) : null,
            'title' => $data['title'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
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
            'invoice' => null,
            'type' => $type,
            'number' => Invoice::nextNumber($type),
            'today' => now()->toDateString(),
            'selectedPhotographer' => $r->query('photographer') ? (int) $r->query('photographer') : null,
        ] + $this->lookups());
    }

    public function store(Request $r, InvoiceService $service)
    {
        $type = $r->input('type') === 'expense' ? 'expense' : 'income';
        $rules = $this->rules();
        if ($type === 'income') $rules['customer_id'] = 'required|exists:customers,id';
        $data = $r->validate($rules, $this->messages());

        try {
            $invoice = $service->create(['type' => $type] + $this->partyFields($type, $data), $data['items']);
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

    public function edit(Invoice $invoice)
    {
        return Inertia::render('Invoices/Create', [
            'invoice' => $invoice->load('items'),
            'type' => $invoice->type,
            'number' => $invoice->number,
            'today' => now()->toDateString(),
            'selectedPhotographer' => null,
        ] + $this->lookups());
    }

    public function update(Request $r, Invoice $invoice, InvoiceService $service)
    {
        $rules = $this->rules();
        if ($invoice->type === 'income') $rules['customer_id'] = 'required|exists:customers,id';
        $data = $r->validate($rules, $this->messages());

        try {
            $service->update($invoice, $this->partyFields($invoice->type, $data), $data['items']);
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'تعذّر حفظ التعديلات، لم يتغير شيء. حاول مرة أخرى.');
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'تم تعديل الفاتورة '.$invoice->number);
    }

    public function destroy(Invoice $invoice)
    {
        $type = $invoice->type;
        $invoice->delete();
        return redirect()->route('invoices.index', ['type' => $type])->with('success', 'تم حذف الفاتورة');
    }
}
