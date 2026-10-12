@extends('layouts.app')
@section('title', 'المصورون')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="page-title">المصورون</h1>
  <a href="{{ route('photographers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> مصور جديد</a>
</div>
<div class="panel table-responsive">
  <table class="table mb-0 align-middle">
    <thead><tr><th>الاسم</th><th>الهاتف</th><th>الأجرة الافتراضية</th><th>إجمالي ما دُفع له</th><th></th></tr></thead>
    <tbody>
    @forelse($photographers as $p)
      <tr>
        <td>{{ $p->name }}</td><td>{{ $p->phone }}</td>
        <td>{{ $p->default_rate ? number_format($p->default_rate, 2) : '—' }}</td>
        <td>{{ number_format($p->paid ?? 0, 2) }}</td>
        <td class="text-end text-nowrap">
          <a href="{{ route('invoices.create', ['type'=>'expense', 'photographer'=>$p->id]) }}" class="btn btn-sm btn-primary">فاتورة أجر</a>
          <a href="{{ route('photographers.edit', $p) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="{{ route('photographers.destroy', $p) }}" class="d-inline" onsubmit="return confirm('حذف هذا المصور؟')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5" class="text-center text-secondary py-4">لا يوجد مصورون. أضف مصورًا لتسجل أجره كصادر.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div class="mt-3">{{ $photographers->links() }}</div>
@endsection
