@extends('layouts.guest')
@section('content')
<h1 class="h4 fw-bold mb-4">تسجيل الدخول</h1>
@if(session('status'))<div class="alert alert-info py-2">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('login') }}">
  @csrf
  <div class="mb-3">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3">
    <label class="form-label">كلمة المرور</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="remember" id="remember">
    <label class="form-check-label" for="remember">تذكرني</label>
  </div>
  <button class="btn btn-primary w-100">دخول</button>
  <p class="text-center mt-3 mb-0">ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب</a></p>
</form>
@endsection
