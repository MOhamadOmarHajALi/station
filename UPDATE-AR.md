# تحديث Station — العملة، تعديل الفواتير، صور الموقع، إلغاء التسجيل، 3 مدراء

## 0) قبل النسخ (مهم)
هذا التحديث يستبدل ملفين فيهما بيانات قد تكون عدّلتها بنفسك، فاحتفظ بها وأعد وضعها بعد النسخ:
- `resources/js/Pages/Landing.vue` ← بياناتك في القسم ✏️ أعلى الملف (الهاتف، البريد، العنوان، واتساب، السوشيال، الأرقام).
- `config/station.php` ← أسماء الشركاء الثلاثة ونسبهم.

## 1) انسخ الملفات
فك الضغط فوق المشروع واستبدل الملفات عند السؤال.

## 2) احذف ما لم يعد مستخدمًا (PowerShell)
```powershell
Remove-Item app\Http\Controllers\UserController.php -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force resources\js\Pages\Users -ErrorAction SilentlyContinue
Remove-Item resources\views\auth\register.blade.php -ErrorAction SilentlyContinue
```
ثم افتح `routes/auth.php` واحذف هذين السطرين (إلغاء التسجيل نهائيًا):
```php
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
```
(وإن بقي سطر `use App\Http\Controllers\Auth\RegisteredUserController;` فلا مشكلة.)
> حتى لو نسيت هذه الخطوة فالمسار `/register` مُغلق (404) من `routes/web.php`.

## 3) متغيرات Railway (Variables)
```
STATION_CURRENCY=$
ADMIN_EMAILS=first@mail.com,second@mail.com,third@mail.com
ADMIN_PASSWORD=كلمة-مرور-قوية
```
- إن كان عندك متغير `STATION_CURRENCY` قديم (د.ع) فغيّره إلى `$` أو احذفه.
- المسموح 3 إيميلات فقط، كلهم مدراء بنفس الصلاحيات، ولا توجد أدوار.
- أي حساب آخر (حتى لو كان موجودًا في قاعدة البيانات) يُطرد عند محاولة الدخول.

## 4) Pre-deploy Command في Railway
```
php artisan migrate --force && php artisan station:admins
```
- `station:admins` ينشئ حسابات الإيميلات الثلاثة بكلمة `ADMIN_PASSWORD`، ولا يغيّر كلمة مرور حساب موجود.
- لتغيير كلمات المرور لاحقًا: غيّر `ADMIN_PASSWORD` وضع مؤقتًا `php artisan station:admins --reset` في الـ Pre-deploy لنشر واحد فقط ثم أعده كما كان.

## 5) Git
```bash
git add .
git commit -m "العملة دولار، تعديل الفواتير، صور الموقع من الداشبورد، إلغاء التسجيل، 3 مدراء"
git push
```

## الجديد
- **العملة:** دولار `$` افتراضيًا (تُغيَّر من `STATION_CURRENCY`).
- **تعديل الفواتير:** زر ✏️ في قائمة الفواتير وفي صفحة الفاتورة. الرقم والنوع (وارد/صادر) لا يتغيران، ويُعاد حساب الإجمالي.
- **صور الموقع:** صفحة جديدة في القائمة الجانبية: رفع عدة صور دفعة واحدة (سحب وإفلات)، تصنيف، تعديل العنوان أو استبدال الصورة، إخفاء/إظهار، ترتيب بالأسهم، وحذف. وزر "عرض الموقع" للمعاينة.
  - الصور تُحفظ داخل قاعدة البيانات لأن ملفات Railway تُمسح عند كل نشر.
  - تُصغَّر تلقائيًا في المتصفح قبل الرفع (أقصى 1600px).
  - إن لم ترفع صورًا بعد، تظهر صور مؤقتة في الموقع.
- **التسجيل ملغى**، والدخول للإيميلات الثلاثة فقط، والتنبيهات (رسائل الموقع والتقرير الشهري) تصل لهذه الإيميلات.
