@extends('layouts.app')
@section('title', $photographer->exists ? 'تعديل مصور' : 'مصور جديد')
@section('content')
<h1 class="page-title mb-3">{{ $photographer->exists ? 'تعديل مصور' : 'مصور جديد' }}</h1>
<form method="POST" class="panel p-4" style="max-width:640px"
      action="{{ $photographer->exists ? route('photographers.update', $photographer) : route('photographers.store') }}">
  @csrf @if($photographer->exists) @method('PUT') @endif
  <div class="mb-3"><label class="form-label">الاسم</label>
    <input name="name" value="{{ old('name', $photographer->name) }}" class="form-control" required></div>
  <div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">الهاتف</label>
      <input name="phone" value="{{ old('phone', $photographer->phone) }}" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">الأجرة الافتراضية</label>
      <input type="number" step="0.01" name="default_rate" value="{{ old('default_rate', $photographer->default_rate) }}" class="form-control"></div>
  </div>
  <button class="btn btn-primary">حفظ</button>
  <a href="{{ route('photographers.index') }}" class="btn btn-link">إلغاء</a>
</form>
@endsection
