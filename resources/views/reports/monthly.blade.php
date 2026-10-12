@extends('layouts.app')
@section('title', 'التقرير الشهري')
@section('content')
@php $cur = config('station.currency'); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <h1 class="page-title">التقرير الشهري — {{ $start->translatedFormat('F Y') }}</h1>
  <form method="GET" class="d-flex gap-2">
    <input type="month" name="month" value="{{ $month }}" class="form-control">
    <button class="btn btn-primary">عرض</button>
    <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i class="bi bi-printer"></i></button>
  </form>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">الواردات</div><div class="fs-3 fw-bold" style="color:var(--teal)">{{ number_format($income, 2) }}</div></div></div>
  <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">الصادرات</div><div class="fs-3 fw-bold" style="color:var(--red)">{{ number_format($expense, 2) }}</div></div></div>
  <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">صافي الربح</div><div class="fs-3 fw-bold">{{ number_format($profit, 2) }} <small class="fs-6">{{ $cur }}</small></div></div></div>
</div>

<div class="panel mb-4">
  <div class="panel-h">توزيع الأرباح</div>
  <table class="table mb-0">
    <thead><tr><th>الشريك</th><th>النسبة</th><th>المبلغ</th></tr></thead>
    @foreach($partners as $p)
      <tr><td>{{ $p['name'] }}</td><td>{{ $p['percent'] }}%</td><td>{{ number_format($p['amount'], 2) }}</td></tr>
    @endforeach
  </table>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="panel"><div class="panel-h">فواتير الواردات</div>
      <table class="table table-sm mb-0">
        @forelse($incomeInvoices as $i)
          <tr><td><a href="{{ route('invoices.show', $i) }}">{{ $i->number }}</a></td><td>{{ $i->customer?->name }}</td><td>{{ $i->invoice_date->format('m-d') }}</td><td>{{ number_format($i->total, 2) }}</td></tr>
        @empty<tr><td class="text-secondary p-3">لا توجد واردات في هذا الشهر.</td></tr>@endforelse
      </table></div>
  </div>
  <div class="col-lg-6">
    <div class="panel"><div class="panel-h">فواتير الصادرات</div>
      <table class="table table-sm mb-0">
        @forelse($expenseInvoices as $i)
          <tr><td><a href="{{ route('invoices.show', $i) }}">{{ $i->number }}</a></td><td>{{ $i->photographer?->name ?? $i->title }}</td><td>{{ $i->invoice_date->format('m-d') }}</td><td>{{ number_format($i->total, 2) }}</td></tr>
        @empty<tr><td class="text-secondary p-3">لا توجد صادرات في هذا الشهر.</td></tr>@endforelse
      </table></div>
  </div>
</div>
@endsection
