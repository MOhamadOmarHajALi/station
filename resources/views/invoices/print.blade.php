<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"><title>{{ $invoice->number }}</title>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
  @vite(['resources/js/app.js'])
  <style>body{background:#fff}.sheet{max-width:780px;margin:auto}@media print{.d-print-none{display:none!important}}</style>
</head>
<body class="p-4">
<div class="sheet">
  <div class="d-flex justify-content-between align-items-start border-bottom border-2" style="border-color:#174E77!important pb-3 mb-4">
    <div class="d-flex align-items-center gap-3"><div style="background:#174E77;padding:10px 14px;border-radius:8px;-webkit-print-color-adjust:exact;print-color-adjust:exact"><img src="{{ asset('images/station-mark.png') }}" height="56" alt=""></div><div><h1 class="fw-bold mb-0">Station</h1><div class="text-secondary">مكتب تصوير</div></div></div>
    <div class="text-start">
      <div class="fw-bold fs-5">{{ $invoice->type === 'income' ? 'فاتورة مبيعات' : 'فاتورة مصروف' }}</div>
      <div>رقم: {{ $invoice->number }}</div>
      <div>التاريخ: {{ $invoice->invoice_date->format('Y-m-d') }}</div>
    </div>
  </div>
  <p class="fs-5">{{ $invoice->type === 'income' ? 'الزبون: '.$invoice->customer?->name : 'المستفيد: '.($invoice->photographer?->name ?? $invoice->title) }}</p>
  <table class="table">
    <thead><tr><th>الوصف</th><th>الكمية</th><th>السعر</th><th>المجموع</th></tr></thead>
    @foreach($invoice->items as $it)
      <tr><td>{{ $it->description }}</td><td>{{ $it->quantity + 0 }}</td><td>{{ number_format($it->price, 2) }}</td><td>{{ number_format($it->subtotal, 2) }}</td></tr>
    @endforeach
    <tfoot><tr><th colspan="3" class="text-end">الإجمالي</th><th>{{ number_format($invoice->total, 2) }} {{ config('station.currency') }}</th></tr></tfoot>
  </table>
  @if($invoice->notes)<p class="text-secondary">ملاحظات: {{ $invoice->notes }}</p>@endif
  <button class="btn btn-primary d-print-none" onclick="window.print()">طباعة</button>
</div>
</body>
</html>
