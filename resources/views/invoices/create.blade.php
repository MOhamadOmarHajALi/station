@extends('layouts.app')
@section('title', 'فاتورة جديدة')
@section('content')
<h1 class="page-title mb-1">{{ $type === 'income' ? 'فاتورة وارد' : 'فاتورة صادر' }}</h1>
<div class="text-secondary mb-3">رقم الفاتورة: {{ $number }}</div>

<form method="POST" action="{{ route('invoices.store') }}" class="panel p-4">
  @csrf
  <input type="hidden" name="type" value="{{ $type }}">
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label">التاريخ</label>
      <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}" class="form-control" required>
    </div>
    @if($type === 'income')
      <div class="col-md-5">
        <label class="form-label">الزبون</label>
        <select name="customer_id" class="form-select" required>
          <option value="">— اختر الزبون —</option>
          @foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
      </div>
    @else
      <div class="col-md-4">
        <label class="form-label">المصور</label>
        <select name="photographer_id" id="photographer" class="form-select">
          <option value="">— بدون مصور —</option>
          @foreach($photographers as $p)
            <option value="{{ $p->id }}" data-rate="{{ $p->default_rate }}" @selected(old('photographer_id', $selectedPhotographer) == $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">البيان (للمصاريف الأخرى)</label>
        <input name="title" value="{{ old('title') }}" class="form-control" placeholder="مثال: أجرة تصوير حفل">
      </div>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table align-middle" id="items">
      <thead><tr><th>الوصف</th><th style="width:110px">الكمية</th><th style="width:150px">السعر</th><th style="width:140px">المجموع</th><th style="width:50px"></th></tr></thead>
      <tbody></tbody>
      <tfoot><tr><th colspan="3" class="text-end">الإجمالي</th><th id="grand">0.00</th><th></th></tr></tfoot>
    </table>
  </div>
  <button type="button" class="btn btn-outline-dark mb-3" id="addRow"><i class="bi bi-plus"></i> إضافة بند</button>

  <div class="mb-3"><label class="form-label">ملاحظات</label>
    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea></div>
  <button class="btn btn-primary">حفظ الفاتورة</button>
  <a href="{{ route('invoices.index', ['type'=>$type]) }}" class="btn btn-link">إلغاء</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  let idx = 0;
  const tbody = document.querySelector('#items tbody');
  const grand = document.getElementById('grand');
  const AR = '٠١٢٣٤٥٦٧٨٩', PR = '۰۱۲۳۴۵۶۷۸۹';

  // يقبل الأرقام العربية والفواصل ويحوّلها لأرقام إنجليزية صالحة
  const clean = v => {
    const s = v.replace(/[٠-٩]/g, d => AR.indexOf(d)).replace(/[۰-۹]/g, d => PR.indexOf(d))
               .replace(/٫/g, '.').replace(/[^0-9.]/g, '');
    const [h, ...t] = s.split('.');
    return t.length ? h + '.' + t.join('') : h;
  };

  function addRow(desc = '', price = '') {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input name="items[${idx}][description]" class="form-control" value="${desc}" required></td>
      <td><input name="items[${idx}][quantity]" type="text" inputmode="decimal" dir="ltr" value="1" class="form-control num qty"></td>
      <td><input name="items[${idx}][price]" type="text" inputmode="decimal" dir="ltr" value="${price}" placeholder="0" class="form-control num price" required></td>
      <td class="sub">0.00</td>
      <td><button type="button" class="btn btn-sm btn-outline-danger rm">×</button></td>`;
    tbody.appendChild(tr); idx++; calc();
  }
  function calc() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach(tr => {
      const s = (parseFloat(tr.querySelector('.qty').value) || 0) * (parseFloat(tr.querySelector('.price').value) || 0);
      tr.querySelector('.sub').textContent = s.toFixed(2); total += s;
    });
    grand.textContent = total.toFixed(2);
  }
  document.getElementById('addRow').onclick = () => addRow();
  tbody.addEventListener('input', e => {
    if (e.target.classList.contains('num')) {
      const c = clean(e.target.value);
      if (c !== e.target.value) e.target.value = c;
    }
    calc();
  });
  tbody.addEventListener('focusin', e => { if (e.target.classList.contains('num')) e.target.select(); });
  tbody.addEventListener('focusout', e => {
    if (e.target.classList.contains('qty') && !parseFloat(e.target.value)) { e.target.value = 1; calc(); }
  });
  tbody.addEventListener('click', e => {
    if (e.target.classList.contains('rm') && tbody.children.length > 1) { e.target.closest('tr').remove(); calc(); }
  });

  // فاتورة مصور: تعبئة البند الأول بأجرته الافتراضية
  const sel = document.getElementById('photographer');
  const rate = () => sel && sel.selectedOptions[0] ? (sel.selectedOptions[0].dataset.rate || '') : '';
  const r0 = rate();
  addRow(sel && sel.value ? 'أجرة تصوير' : '', r0 ? parseFloat(r0) : '');
  if (sel) sel.addEventListener('change', () => {
    const first = tbody.querySelector('tr'), r = rate();
    if (first && r) {
      const d = first.querySelector('[name$="[description]"]');
      if (!d.value) d.value = 'أجرة تصوير';
      first.querySelector('.price').value = parseFloat(r); calc();
    }
  });
});
</script>
@endsection
