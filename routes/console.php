<?php

use Illuminate\Support\Facades\Schedule;

// ⚙️ الأتمتة: تعمل تلقائيًا عند تشغيل php artisan schedule:work (محليًا) أو cron (على السيرفر)
Schedule::command('station:run-recurring')->dailyAt('06:00')->withoutOverlapping();
Schedule::command('station:monthly-report')->monthlyOn(1, '08:00')->withoutOverlapping();
