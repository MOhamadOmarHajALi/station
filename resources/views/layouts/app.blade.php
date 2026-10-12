<!DOCTYPE html>
@php
    $appUser = auth()->user();
@endphp

<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Station')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/js/app.js'])
</head>
<body>
<div class="shell">
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="side">
    <div class="offcanvas-body">
      <div class="brand"><img src="{{ asset('images/station-mark.png') }}" width="50" height="70" class="brand-mark" alt=""><div><b>Station</b><small>مكتب تصوير</small></div></div>
      <nav class="side-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> لوحة التحكم</a>
        <a href="{{ route('invoices.index', ['type'=>'income']) }}" class="{{ request('type','income')==='income' && request()->routeIs('invoices.*') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down"></i> الواردات</a>
        <a href="{{ route('invoices.index', ['type'=>'expense']) }}" class="{{ request('type')==='expense' && request()->routeIs('invoices.index') ? 'active' : '' }}"><i class="bi bi-box-arrow-up"></i> الصادرات</a>
        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="bi bi-people"></i> الزبائن</a>
        <a href="{{ route('photographers.index') }}" class="{{ request()->routeIs('photographers.*') ? 'active' : '' }}"><i class="bi bi-camera"></i> المصورون</a>
        <a href="{{ route('reports.monthly') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-calendar3"></i> التقرير الشهري</a>
      </nav>
      <div class="side-foot">
        @if($appUser)
          <div class="mb-2">{{ $appUser->name }}</div>
        @else
          <div class="mb-2">زائر</div>
        @endif
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-left"></i> تسجيل الخروج</button>
        </form>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="d-lg-none mb-3 d-flex justify-content-between align-items-center">
      <b class="fs-5">Station</b>
      <button class="btn btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#side"><i class="bi bi-list"></i></button>
    </div>
    @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
    @if(session('warning'))<div class="alert alert-warning py-2">{{ session('warning') }}</div>@endif
    @if($errors && $errors->any())
      <div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
  </main>
</div>
</body>
</html>
