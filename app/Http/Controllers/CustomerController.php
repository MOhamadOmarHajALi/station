<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    private function rules(): array
    {
        return ['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email', 'notes' => 'nullable|string'];
    }

    public function index()
    {
        return Inertia::render('Customers/Index', [
            'customers' => Customer::withCount('invoices')
                ->withSum(['invoices as billed' => fn ($q) => $q->where('type', 'income')], 'total')
                ->latest()->paginate(20),
        ]);
    }

    public function create() { return Inertia::render('Customers/Form', ['customer' => null]); }

    public function store(Request $r)
    {
        Customer::create($r->validate($this->rules()));
        return redirect()->route('customers.index')->with('success', 'تمت إضافة الزبون');
    }

    public function edit(Customer $customer) { return Inertia::render('Customers/Form', ['customer' => $customer]); }

    public function update(Request $r, Customer $customer)
    {
        $customer->update($r->validate($this->rules()));
        return redirect()->route('customers.index')->with('success', 'تم حفظ التعديلات');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'تم حذف الزبون');
    }
}
