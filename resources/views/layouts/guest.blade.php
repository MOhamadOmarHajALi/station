<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Station</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/js/app.js'])
</head>
<body>
<div class="guest">
  <div class="guest-art"><div><img src="{{ asset('images/station-logo.png') }}" width="150px" height="200px" alt="Station"><p class="fs-5 mt-4 mb-0" style="color:#BCD3E5">حسابات مكتب التصوير في مكان واحد</p></div></div>
  <div class="guest-form"><div class="w-100" style="max-width:380px"><div class="text-center mb-4 d-lg-none"><span class="guest-logo"><img src="{{ asset('images/station-mark.png') }}" width="56" height="56" alt="Station"></span></div>@yield('content')</div></div>
</div>
</body>
</html>
