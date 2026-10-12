@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('content')
@php
  $cur = config('station.currency');
  $fmt = fn($n) => number_format($n, 0);
  $deltaView = function ($d, $goodWhenUp = true) {
      if ($d === null) return ['flat', 'لا توجد بيانات للشهر السابق'];
      if ($d == 0) return ['flat', 'مثل الشهر السابق'];
      $up = $d > 0;
      $good = $up === $goodWhenUp;
      return [$good ? 'up' : 'down', ($up ? '▲ ' : '▼ ').abs($d).'% عن الشهر السابق'];
  };
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div>
    <h1 class="page-title">{{ $start->translatedFormat('F Y') }}</h1>
    <div class="text-secondary">{{ $counts['invoices'] }} فاتورة هذا الشهر · {{ $counts['customers'] }} زبون · {{ $counts['photographers'] }} مصور</div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <form method="GET" class="d-flex gap-2">
      <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()">
    </form>
    <a href="{{ route('invoices.create', ['type'=>'income']) }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> فاتورة وارد</a>
    <a href="{{ route('invoices.create', ['type'=>'expense']) }}" class="btn btn-outline-dark"><i class="bi bi-plus-lg"></i> فاتورة صادر</a>
  </div>
</div>

{{-- شريط الفيلم --}}
<div class="strip mb-4">
  <div class="row g-3">
    <div class="col-lg-4 col-md-6">
      @php [$c, $t] = $deltaView($deltas['income'], true); @endphp
      <div class="frame">
        <div class="lbl">الواردات</div>
        <div class="val v-in">{{ $fmt($income) }} <small class="fs-6">{{ $cur }}</small></div>
        <div class="delta {{ $c }}">{{ $t }}</div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6">
      @php [$c, $t] = $deltaView($deltas['expense'], false); @endphp
      <div class="frame">
        <div class="lbl">الصادرات</div>
        <div class="val v-ex">{{ $fmt($expense) }} <small class="fs-6">{{ $cur }}</small></div>
        <div class="delta {{ $c }}">{{ $t }}</div>
      </div>
    </div>
    <div class="col-lg-4">
      @php [$c, $t] = $deltaView($deltas['profit'], true); @endphp
      <div class="frame profit">
        <div class="lbl">صافي الربح</div>
        <div class="val {{ $profit < 0 ? 'v-ex' : '' }}">{{ $fmt($profit) }} <small class="fs-6">{{ $cur }}</small></div>
        <div class="delta {{ $c }}">{{ $t }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="panel h-100">
      <div class="panel-h">الواردات والصادرات آخر 6 أشهر</div>
      <div class="p-3"><div class="chart-box"><canvas id="trend"></canvas></div></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel h-100">
      <div class="panel-h">توزيع الأرباح</div>
      <div class="p-3">
        @php $colors = ['#174E77', '#1F7A6B', '#E8A33D', '#6b7482']; @endphp
        <div class="split mb-3">
          @foreach($partners as $i => $p)
            <div style="width:{{ $p['percent'] }}%;background:{{ $colors[$i % 4] }}"></div>
          @endforeach
        </div>
        @foreach($partners as $i => $p)
          <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div><span class="dot" style="background:{{ $colors[$i % 4] }}"></span>{{ $p['name'] }} <span class="text-secondary">({{ $p['percent'] }}%)</span></div>
            <b>{{ $fmt($p['amount']) }}</b>
          </div>
        @endforeach
        @if($profit <= 0)
          <div class="text-secondary small mt-3">لا يوجد ربح لتوزيعه في هذا الشهر.</div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-h">آخر الفواتير <a href="{{ route('reports.monthly', ['month'=>$month]) }}" class="small fw-normal">التقرير الشهري</a></div>
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead><tr><th>الرقم</th><th>الجهة</th><th>التاريخ</th><th>المبلغ</th></tr></thead>
          <tbody>
          @forelse($recent as $i)
            <tr>
              <td><a href="{{ route('invoices.show', $i) }}">{{ $i->number }}</a></td>
              <td>{{ $i->type === 'income' ? $i->customer?->name : ($i->photographer?->name ?? $i->title) }}</td>
              <td>{{ $i->invoice_date->format('Y-m-d') }}</td>
              <td><span class="badge {{ $i->type === 'income' ? 'badge-in' : 'badge-ex' }}">{{ $i->type === 'income' ? '+' : '−' }}{{ $fmt($i->total) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary py-4">لا توجد فواتير بعد. ابدأ بإنشاء فاتورة وارد.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="panel mb-4">
      <div class="panel-h">أعلى الزبائن هذا الشهر</div>
      <ul class="list-group list-group-flush">
        @forelse($topCustomers as $c)
          <li class="list-group-item d-flex justify-content-between"><span>{{ $c->name }}</span><b>{{ $fmt($c->month_total) }}</b></li>
        @empty
          <li class="list-group-item text-secondary">لا توجد مبيعات هذا الشهر.</li>
        @endforelse
      </ul>
    </div>
    <div class="panel">
      <div class="panel-h">أجور المصورين هذا الشهر</div>
      <ul class="list-group list-group-flush">
        @forelse($photographerCosts as $p)
          <li class="list-group-item d-flex justify-content-between"><span>{{ $p->name }}</span><b>{{ $fmt($p->month_total) }}</b></li>
        @empty
          <li class="list-group-item text-secondary">لا توجد أجور مسجلة هذا الشهر.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const d = @json($chart);
  const font = { family: 'IBM Plex Sans Arabic' };
  new Chart(document.getElementById('trend'), {
    type: 'bar',
    data: {
      labels: d.labels,
      datasets: [
        { label: 'الواردات', data: d.income, backgroundColor: '#1F7A6B', borderRadius: 3 },
        { label: 'الصادرات', data: d.expense, backgroundColor: '#C8362D', borderRadius: 3 },
      ],
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font } } },
      scales: {
        x: { grid: { display: false }, ticks: { font } },
        y: { beginAtZero: true, grid: { color: '#e6e8ec' }, ticks: { font } },
      },
    },
  });
});
</script>
@endsection
