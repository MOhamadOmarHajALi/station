@extends('layouts.guest')
@section('content')
<h1 class="h4 fw-bold mb-4">إنشاء حساب</h1>
<form method="POST" action="{{ route('register') }}">
  @csrf
  <div class="mb-3">
    <label class="form-label">الاسم</label>
    <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3">
    <label class="form-label">كلمة المرور</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3">
    <label class="form-label">تأكيد كلمة المرور</label>
    <input type="password" name="password_confirmation" class="form-control" required>
  </div>
  <button class="btn btn-primary w-100">إنشاء الحساب</button>
  <p class="text-center mt-3 mb-0">لديك حساب؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
</form>
@endsection
