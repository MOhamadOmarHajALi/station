<?php

return [
    'currency' => env('STATION_CURRENCY', '$'),

    // توزيع الأرباح (يجب أن يكون المجموع 100)
    'partners' => [
        ['name' => 'الشريك الأول',  'percent' => 30],
        ['name' => 'الشريك الثاني', 'percent' => 30],
        ['name' => 'الشريك الثالث', 'percent' => 40],
    ],

    // الإيميلات الثلاثة المسموح لها بدخول النظام (كلها مدراء). تُضبط من متغير ADMIN_EMAILS مفصولة بفاصلة
    'admin_emails' => array_values(array_filter(array_map(
        fn ($e) => strtolower(trim($e)),
        explode(',', (string) env('ADMIN_EMAILS', ''))
    ))),

    // كلمة المرور الابتدائية عند إنشاء الحسابات بالأمر station:admins
    'admin_password' => env('ADMIN_PASSWORD'),
];
