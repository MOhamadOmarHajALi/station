# Station — نسخة Vue (Inertia) — خطوات التركيب

## 1) مشروع جديد (لا تخلطه بمشروع Blade القديم)
```powershell
composer create-project laravel/laravel station-vue
cd station-vue
composer require laravel/breeze --dev
php artisan breeze:install vue
```
(اختر الافتراضي لباقي الأسئلة. هذا يثبّت Inertia وVue وصفحات الدخول.)

## 2) فك الضغط فوق المشروع واستبدل الملفات
ثم احذف ما لا نستخدمه: `postcss.config.js` و`tailwind.config.js` و`resources/css/app.css` (اختياري، لا يضر وجودها لأن Tailwind مثبّت).

## 3) الحزم
```powershell
npm install bootstrap @popperjs/core bootstrap-icons chart.js
```

## 4) bootstrap/app.php — أضف معالجة الأخطاء داخل withExceptions
ضع هذا بدل `->withExceptions(function (Exceptions $exceptions) { ... })` الموجود (وأضف الـ use في أعلى الملف):
```php
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
        $status = $response->getStatusCode();

        if (in_array($status, [500, 503]) && app()->environment(['local', 'testing'])) {
            return $response; // أثناء التطوير أظهر صفحة الخطأ التفصيلية
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
})
```

## 5) .env
```
APP_LOCALE=ar
STATION_CURRENCY="د.ع"
MAIL_MAILER=log          # غيّره إلى smtp لإرسال تقرير بريد حقيقي
```

## 6) التشغيل
```powershell
php artisan migrate
npm run dev
php artisan serve
# في نافذة ثالثة: تشغيل الأتمتة محليًا
php artisan schedule:work
```
أول حساب تسجله من `/register` يصبح **مديرًا** تلقائيًا.

## اختبار الأتمتة يدويًا
```powershell
php artisan station:run-recurring
php artisan station:monthly-report --month=2026-10
```
(مع MAIL_MAILER=log يظهر البريد داخل storage/logs/laravel.log)

## على السيرفر (Linux) أضف سطر cron واحد
```
* * * * * cd /path/to/station-vue && php artisan schedule:run >> /dev/null 2>&1
```
