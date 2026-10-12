<?php

return [
    'currency' => env('STATION_CURRENCY', 'د.ع'),

    // توزيع الأرباح (يجب أن يكون المجموع 100)
    'partners' => [
        ['name' => 'الشريك الأول',  'percent' => 30],
        ['name' => 'الشريك الثاني', 'percent' => 30],
        ['name' => 'الشريك الثالث', 'percent' => 40],
    ],
];
