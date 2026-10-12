@extends('layouts.app')
@section('title', 'الزبائن')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="page-title">الزبائن</h1>
  <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> زبون جديد</a>
</div>
<div class="panel table-responsive">
  <table class="table mb-0 align-middle">
    <thead><tr><th>الاسم</th><th>الهاتف</th><th>الفواتير</th><th>إجمالي المبيعات</th><th></th></tr></thead>
    <tbody>
    @forelse($customers as $c)
      <tr>
        <td>{{ $c->name }}</td><td>{{ $c->phone }}</td><td>{{ $c->invoices_count }}</td>
        <td>{{ number_format($c->billed ?? 0, 2) }}</td>
        <td class="text-end text-nowrap">
          <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="{{ route('customers.destroy', $c) }}" class="d-inline" onsubmit="return confirm('حذف هذا الزبون؟')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-secondary py-4">لا يوجد زبائن. أضف أول زبون لتبدأ بإصدار الفواتير.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
