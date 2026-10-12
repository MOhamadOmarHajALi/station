@extends('layouts.app')
@section('title', $customer->exists ? 'تعديل زبون' : 'زبون جديد')
@section('content')
<h1 class="page-title mb-3">{{ $customer->exists ? 'تعديل زبون' : 'زبون جديد' }}</h1>
<form method="POST" class="panel p-4" style="max-width:640px"
      action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
  @csrf @if($customer->exists) @method('PUT') @endif
  <div class="mb-3"><label class="form-label">الاسم</label>
    <input name="name" value="{{ old('name', $customer->name) }}" class="form-control" required></div>
  <div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">الهاتف</label>
      <input name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">البريد الإلكتروني</label>
      <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control"></div>
  </div>
  <div class="mb-3"><label class="form-label">ملاحظات</label>
    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $customer->notes) }}</textarea></div>
  <button class="btn btn-primary">حفظ</button>
  <a href="{{ route('customers.index') }}" class="btn btn-link">إلغاء</a>
</form>
@endsection
