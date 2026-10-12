@extends('layouts.app')
@section('title', $invoice->number)
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="page-title">{{ $invoice->number }}
    <span class="badge fs-6 {{ $invoice->type === 'income' ? 'badge-in' : 'badge-ex' }}">{{ $invoice->type === 'income' ? 'وارد' : 'صادر' }}</span></h1>
  <div class="d-flex gap-2">
    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-primary"><i class="bi bi-printer"></i> طباعة</a>
    <a href="{{ route('invoices.index', ['type'=>$invoice->type]) }}" class="btn btn-outline-dark">القائمة</a>
  </div>
</div>
<div class="panel p-4">
  <div class="row mb-3">
    <div class="col-md-4"><div class="text-secondary">التاريخ</div>{{ $invoice->invoice_date->format('Y-m-d') }}</div>
    <div class="col-md-4"><div class="text-secondary">{{ $invoice->type === 'income' ? 'الزبون' : 'المصور / البيان' }}</div>
      {{ $invoice->type === 'income' ? $invoice->customer?->name : ($invoice->photographer?->name ?? $invoice->title ?? '—') }}</div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead><tr><th>الوصف</th><th>الكمية</th><th>السعر</th><th>المجموع</th></tr></thead>
      @foreach($invoice->items as $it)
        <tr><td>{{ $it->description }}</td><td>{{ $it->quantity + 0 }}</td><td>{{ number_format($it->price, 2) }}</td><td>{{ number_format($it->subtotal, 2) }}</td></tr>
      @endforeach
      <tfoot><tr><th colspan="3" class="text-end">الإجمالي</th><th>{{ number_format($invoice->total, 2) }} {{ config('station.currency') }}</th></tr></tfoot>
    </table>
  </div>
  @if($invoice->notes)<div class="text-secondary">ملاحظات: {{ $invoice->notes }}</div>@endif
</div>
@endsection
