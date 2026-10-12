<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhotographerController extends Controller
{
    private function rules(): array
    {
        return ['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:50',
                'default_rate' => 'nullable|numeric|min:0'];
    }

    public function index()
    {
        return Inertia::render('Photographers/Index', [
            'photographers' => Photographer::withCount('invoices')
                ->withSum(['invoices as paid' => fn ($q) => $q->where('type', 'expense')], 'total')
                ->latest()->paginate(20),
        ]);
    }

    public function create() { return Inertia::render('Photographers/Form', ['photographer' => null]); }

    public function store(Request $r)
    {
        Photographer::create($r->validate($this->rules()));
        return redirect()->route('photographers.index')->with('success', 'تمت إضافة المصور');
    }

    public function edit(Photographer $photographer) { return Inertia::render('Photographers/Form', ['photographer' => $photographer]); }

    public function update(Request $r, Photographer $photographer)
    {
        $photographer->update($r->validate($this->rules()));
        return redirect()->route('photographers.index')->with('success', 'تم حفظ التعديلات');
    }

    public function destroy(Photographer $photographer)
    {
        $photographer->delete();
        return back()->with('success', 'تم حذف المصور');
    }
}
