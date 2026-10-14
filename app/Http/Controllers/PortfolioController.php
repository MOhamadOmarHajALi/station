<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public const CATS = ['wedding' => 'أعراس', 'portrait' => 'بورتريه وعائلي', 'product' => 'منتجات', 'event' => 'فعاليات'];

    private const LIST_COLUMNS = ['id', 'title', 'category', 'is_active', 'sort_order', 'updated_at'];

    /** لوحة الإدارة */
    public function index()
    {
        return Inertia::render('Portfolio/Index', [
            'cats' => self::CATS,
            'items' => PortfolioItem::ordered()->get(self::LIST_COLUMNS)->map(fn ($i) => [
                'id' => $i->id, 'title' => $i->title, 'category' => $i->category,
                'is_active' => $i->is_active, 'src' => $i->url(),
            ]),
        ]);
    }

    /** عرض الصورة للزوار (عام) */
    public function image(PortfolioItem $item)
    {
        return response(base64_decode($item->image_data), 200, [
            'Content-Type' => $item->mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'image' => 'required|image|mimes:jpeg,png,webp|max:6144',
            'category' => 'required|in:'.implode(',', array_keys(self::CATS)),
            'title' => 'nullable|string|max:120',
        ], [
            'image.required' => 'اختر صورة',
            'image.image' => 'الملف ليس صورة صالحة',
            'image.mimes' => 'الصيغ المسموحة: JPG وPNG وWebP',
            'image.max' => 'حجم الصورة كبير (الحد 6 ميغابايت)',
        ]);

        $file = $r->file('image');
        $item = PortfolioItem::create([
            'title' => ($data['title'] ?? null) ?: self::CATS[$data['category']],
            'category' => $data['category'],
            'mime' => $file->getMimeType() ?: 'image/jpeg',
            'image_data' => base64_encode(file_get_contents($file->getRealPath())),
            'sort_order' => ((int) PortfolioItem::max('sort_order')) + 1,
        ]);

        return $r->wantsJson() ? response()->json(['ok' => true, 'id' => $item->id]) : back()->with('success', 'تمت إضافة الصورة');
    }

    public function update(Request $r, PortfolioItem $item)
    {
        $data = $r->validate([
            'title' => 'sometimes|required|string|max:120',
            'category' => 'sometimes|required|in:'.implode(',', array_keys(self::CATS)),
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:6144',
        ]);

        if ($file = $r->file('image')) {
            $item->mime = $file->getMimeType() ?: 'image/jpeg';
            $item->image_data = base64_encode(file_get_contents($file->getRealPath()));
        }
        $item->fill(collect($data)->except('image')->all())->save();

        return back()->with('success', 'تم الحفظ');
    }

    /** تحريك الصورة للأعلى/الأسفل في الترتيب */
    public function move(Request $r, PortfolioItem $item)
    {
        $dir = $r->validate(['direction' => 'required|in:up,down'])['direction'];
        $ids = PortfolioItem::ordered()->pluck('id')->all();
        $i = array_search($item->id, $ids);
        $j = $dir === 'up' ? $i - 1 : $i + 1;

        if (isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $order => $id) {
                PortfolioItem::whereKey($id)->update(['sort_order' => $order]);
            }
        }
        return back();
    }

    public function destroy(PortfolioItem $item)
    {
        $item->delete();
        return back()->with('success', 'تم حذف الصورة');
    }
}
