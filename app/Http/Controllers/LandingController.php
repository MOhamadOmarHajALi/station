<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function show(Request $r)
    {
        // المدير المسجّل يذهب للوحة التحكم، ويستطيع رؤية الموقع بإضافة ?preview=1
        if (auth()->check() && ! $r->boolean('preview')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Landing', [
            'works' => PortfolioItem::active()->ordered()
                ->get(['id', 'title', 'category', 'updated_at'])
                ->map(fn ($i) => ['id' => $i->id, 'title' => $i->title, 'cat' => $i->category, 'src' => $i->url()]),
        ]);
    }

    /** التسجيل مُلغى */
    public function closed()
    {
        abort(404);
    }
}
