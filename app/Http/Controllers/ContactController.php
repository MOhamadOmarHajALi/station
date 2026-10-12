<?php

namespace App\Http\Controllers;

use App\Models\{ContactMessage, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Mail};
use Throwable;

class ContactController extends Controller
{
    /** نموذج "تواصل معنا" العام — بدون تسجيل دخول */
    public function store(Request $r)
    {
        // حقل مخفي يملؤه الروبوتات فقط: نتجاهل الرسالة بصمت
        if (filled($r->input('website'))) {
            return back()->with('success', 'تم استلام رسالتك');
        }

        $data = $r->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:150',
            'service' => 'nullable|string|max:100',
            'message' => 'required|string|min:5|max:3000',
        ], [
            'name.required' => 'اكتب اسمك',
            'phone.required' => 'اكتب رقم هاتفك للتواصل',
            'email.email' => 'صيغة البريد غير صحيحة',
            'message.required' => 'اكتب رسالتك',
            'message.min' => 'الرسالة قصيرة جدًا',
        ]);

        $msg = ContactMessage::create($data);

        // إشعار بالبريد للمدراء (فشل البريد لا يُفقد الرسالة، فهي محفوظة في النظام)
        try {
            $emails = User::where('role', 'admin')->pluck('email')->all();
            if ($emails) {
                Mail::raw(
                    "رسالة جديدة من موقع Station\n\nالاسم: {$msg->name}\nالهاتف: {$msg->phone}\n"
                    ."البريد: ".($msg->email ?: '—')."\nالخدمة: ".($msg->service ?: '—')."\n\n{$msg->message}",
                    fn ($m) => $m->to($emails)->subject('رسالة جديدة: '.$msg->name)
                );
            }
        } catch (Throwable $e) {
            report($e);
            Log::warning('Contact notification mail failed', ['id' => $msg->id]);
        }

        return back()->with('success', 'تم استلام رسالتك، سنتواصل معك قريبًا.');
    }
}
