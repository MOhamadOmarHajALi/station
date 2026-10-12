@extends('layouts.app')
@section('title', $type === 'income' ? 'الواردات' : 'الصادرات')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h1 class="page-title">{{ $type === 'income' ? 'الواردات' : 'الصادرات' }}</h1>
    <div class="text-secondary">الإجمالي: <b>{{ number_format($grand, 2) }}</b> {{ config('station.currency') }}</div>
  </div>
  <a href="{{ route('invoices.create', ['type'=>$type]) }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ $type === 'income' ? 'فاتورة وارد' : 'فاتورة صادر' }}</a>
</div>
<div class="panel table-responsive">
  <table class="table mb-0 align-middle">
    <thead><tr><th>الرقم</th><th>{{ $type === 'income' ? 'الزبون' : 'المصور / البيان' }}</th><th>التاريخ</th><th>المبلغ</th><th></th></tr></thead>
    <tbody>
    @forelse($invoices as $i)
      <tr>
        <td><a href="{{ route('invoices.show', $i) }}">{{ $i->number }}</a></td>
        <td>{{ $type === 'income' ? $i->customer?->name : ($i->photographer?->name ?? $i->title) }}</td>
        <td>{{ $i->invoice_date->format('Y-m-d') }}</td>
        <td>{{ number_format($i->total, 2) }}</td>
        <td class="text-end text-nowrap">
          <a href="{{ route('invoices.print', $i) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="bi bi-printer"></i></a>
          <form method="POST" action="{{ route('invoices.destroy', $i) }}" class="d-inline" onsubmit="return confirm('حذف الفاتورة {{ $i->number }}؟')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-secondary py-4">لا توجد فواتير بعد.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">{{ $invoices->links() }}</div>
@endsection
