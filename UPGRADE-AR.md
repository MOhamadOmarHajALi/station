# ترقية مشروع Station القديم (Blade) إلى Vue + أتمتة + صلاحيات

ما سيحدث: صفحات **تسجيل الدخول والتسجيل تبقى Blade** كما هي (بتصميمك واللوغو)، وكل ما بعد الدخول
(لوحة التحكم، الزبائن، المصورون، الفواتير، التقرير) يصبح Vue، مع الأتمتة والصلاحيات وصفحة الهبوط ومعالجة الأخطاء.

> قبل البدء: خذ نسخة احتياطية من المشروع (أو `git commit`)، وتأكد أن `bootstrap/app.php` موجود (Laravel 11 أو 12).

## 1) فك الضغط فوق المشروع واستبدل الملفات عند السؤال

## 2) احذف ملفات Blade القديمة التي حلّ محلها Vue (PowerShell)
```powershell
Remove-Item -Recurse -Force resources\views\customers, resources\views\photographers, resources\views\invoices, resources\views\reports
Remove-Item resources\views\dashboard.blade.php, resources\views\layouts\app.blade.php -ErrorAction SilentlyContinue
```
(لا تحذف `resources\views\auth` ولا `layouts\guest.blade.php`.)

## 3) الحزم
```powershell
composer require inertiajs/inertia-laravel
npm install vue @inertiajs/vue3 @vitejs/plugin-vue
```
(Bootstrap وchart.js وbootstrap-icons مثبتة عندك من قبل. وإن لم تكن: `npm install bootstrap @popperjs/core bootstrap-icons chart.js`)

## 4) استبدل محتوى `bootstrap/app.php` بهذا (أو ادمجه إن كنت معدّلًا عليه)
```php
<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [HandleInertiaRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            $status = $response->getStatusCode();

            if (in_array($status, [500, 503]) && app()->environment(['local', 'testing'])) {
                return $response; // أثناء التطوير تظهر صفحة الخطأ التفصيلية
            }
            if ($status === 419) {
                return back()->with('error', 'انتهت الجلسة، حاول مرة أخرى.');
            }
            if (in_array($status, [403, 404, 500, 503])) {
                return Inertia::render('Error', ['status' => $status])
                    ->toResponse($request)->setStatusCode($status);
            }
            return $response;
        });
    })->create();
```

## 5) `.env`
```
APP_LOCALE=ar
STATION_CURRENCY="د.ع"
MAIL_MAILER=log
```

## 6) التشغيل
```powershell
php artisan migrate
npm run dev
php artisan serve
php artisan schedule:work      # نافذة ثالثة: تشغيل الأتمتة محليًا
```
- **حسابك الحالي يصبح مديرًا تلقائيًا** (أقدم مستخدم). من صفحة "المستخدمون والصلاحيات" تغيّر أدوار الباقين.
- الزائر غير المسجّل يرى صفحة الهبوط على `/`، والمسجّل يُحوَّل للوحة التحكم.

## اختبار الأتمتة يدويًا
```powershell
php artisan station:run-recurring
php artisan station:monthly-report --month=2026-10
```

## على السيرفر (Linux)
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---
## إضافة: موقع المكتب (Landing) + نموذج تواصل + أبرز أعمالنا
- عدّل بيانات مكتبك (الهاتف، البريد، العنوان، واتساب، السوشيال، الأرقام) من أعلى ملف `resources/js/Pages/Landing.vue` (قسم ✏️).
- **صورك:** ضعها في `public/images/portfolio/` وغيّر المسارات في مصفوفة `works` (الصور الحالية SVG مؤقتة).
- رسائل نموذج "تواصل معنا" تُحفظ في الجدول `contact_messages` وتظهر للمدير/المحاسب في صفحة **رسائل الموقع**، وتصل نسخة بالبريد للمدراء.
- بعد فك الضغط نفّذ `php artisan migrate` (يُنشئ جدول الرسائل).
