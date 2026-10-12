<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        return Inertia::render('Messages/Index', ['messages' => ContactMessage::latest()->paginate(20)]);
    }

    /** تبديل مقروءة / غير مقروءة */
    public function update(ContactMessage $message)
    {
        $message->update(['read_at' => $message->read_at ? null : now()]);
        return back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return back()->with('success', 'تم حذف الرسالة');
    }
}
