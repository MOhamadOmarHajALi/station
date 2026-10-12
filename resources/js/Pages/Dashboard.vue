<script setup>
import { router, Link } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref, computed } from 'vue';
import Chart from 'chart.js/auto';
import AppLayout from '../Layouts/AppLayout.vue';
import { money } from '../format';
import { usePage } from '@inertiajs/vue3';

const p = defineProps({
  month: String, monthLabel: String, income: Number, expense: Number, profit: Number,
  deltas: Object, chart: Object, partners: Array, topCustomers: Array,
  photographerCosts: Array, recent: Array, counts: Object,
});

const cur = computed(() => usePage().props.currency);
const can = computed(() => usePage().props.can);
const m = ref(p.month);
const go = () => router.get('/dashboard', { month: m.value }, { preserveState: false });
const colors = ['#174E77', '#1F7A6B', '#E8A33D', '#6b7482'];

const delta = (d, goodUp) => {
  if (d === null) return ['flat', 'لا توجد بيانات للشهر السابق'];
  if (d === 0) return ['flat', 'مثل الشهر السابق'];
  const good = d > 0 === goodUp;
  return [good ? 'up' : 'down', (d > 0 ? '▲ ' : '▼ ') + Math.abs(d) + '% عن الشهر السابق'];
};

const canvas = ref(null);
let ch;
onMounted(() => {
  const font = { family: 'IBM Plex Sans Arabic' };
  ch = new Chart(canvas.value, {
    type: 'bar',
    data: { labels: p.chart.labels, datasets: [
      { label: 'الواردات', data: p.chart.income, backgroundColor: '#1F7A6B', borderRadius: 3 },
      { label: 'الصادرات', data: p.chart.expense, backgroundColor: '#C8362D', borderRadius: 3 },
    ] },
    options: { responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font } } },
      scales: { x: { grid: { display: false }, ticks: { font } }, y: { beginAtZero: true, grid: { color: '#e6e8ec' }, ticks: { font } } } },
  });
});
onBeforeUnmount(() => ch && ch.destroy());
</script>

<template>
  <AppLayout title="لوحة التحكم">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <div>
        <h1 class="page-title">{{ monthLabel }}</h1>
        <div class="text-secondary">{{ counts.invoices }} فاتورة هذا الشهر · {{ counts.customers }} زبون · {{ counts.photographers }} مصور</div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <input type="month" v-model="m" class="form-control" style="width:auto" @change="go">
        <template v-if="can.manage">
          <Link href="/invoices/create?type=income" class="btn btn-primary"><i class="bi bi-plus-lg"></i> فاتورة وارد</Link>
          <Link href="/invoices/create?type=expense" class="btn btn-outline-dark"><i class="bi bi-plus-lg"></i> فاتورة صادر</Link>
        </template>
      </div>
    </div>

    <div class="strip mb-4">
      <div class="row g-3">
        <div class="col-lg-4 col-md-6"><div class="frame">
          <div class="lbl">الواردات</div>
          <div class="val v-in">{{ money(income, 0) }} <small class="fs-6">{{ cur }}</small></div>
          <div class="delta" :class="delta(deltas.income, true)[0]">{{ delta(deltas.income, true)[1] }}</div>
        </div></div>
        <div class="col-lg-4 col-md-6"><div class="frame">
          <div class="lbl">الصادرات</div>
          <div class="val v-ex">{{ money(expense, 0) }} <small class="fs-6">{{ cur }}</small></div>
          <div class="delta" :class="delta(deltas.expense, false)[0]">{{ delta(deltas.expense, false)[1] }}</div>
        </div></div>
        <div class="col-lg-4"><div class="frame profit">
          <div class="lbl">صافي الربح</div>
          <div class="val" :class="{ 'v-ex': profit < 0 }">{{ money(profit, 0) }} <small class="fs-6">{{ cur }}</small></div>
          <div class="delta" :class="delta(deltas.profit, true)[0]">{{ delta(deltas.profit, true)[1] }}</div>
        </div></div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-8"><div class="panel h-100">
        <div class="panel-h">الواردات والصادرات آخر 6 أشهر</div>
        <div class="p-3"><div class="chart-box"><canvas ref="canvas"></canvas></div></div>
      </div></div>
      <div class="col-lg-4"><div class="panel h-100">
        <div class="panel-h">توزيع الأرباح</div>
        <div class="p-3">
          <div class="split mb-3"><div v-for="(x, i) in partners" :key="i" :style="{ width: x.percent + '%', background: colors[i % 4] }"></div></div>
          <div v-for="(x, i) in partners" :key="i" class="d-flex justify-content-between align-items-center py-2" :class="{ 'border-bottom': i < partners.length - 1 }">
            <div><span class="dot" :style="{ background: colors[i % 4] }"></span>{{ x.name }} <span class="text-secondary">({{ x.percent }}%)</span></div>
            <b>{{ money(x.amount, 0) }}</b>
          </div>
          <div v-if="profit <= 0" class="text-secondary small mt-3">لا يوجد ربح لتوزيعه في هذا الشهر.</div>
        </div>
      </div></div>
    </div>

    <div class="row g-4">
      <div class="col-lg-7"><div class="panel">
        <div class="panel-h">آخر الفواتير <Link :href="'/reports/monthly?month=' + month" class="small fw-normal">التقرير الشهري</Link></div>
        <div class="table-responsive"><table class="table mb-0 align-middle">
          <thead><tr><th>الرقم</th><th>الجهة</th><th>التاريخ</th><th>المبلغ</th></tr></thead>
          <tbody>
            <tr v-for="i in recent" :key="i.id">
              <td><Link :href="'/invoices/' + i.id">{{ i.number }}</Link></td>
              <td>{{ i.party }}</td><td>{{ i.date }}</td>
              <td><span class="badge" :class="i.type === 'income' ? 'badge-in' : 'badge-ex'">{{ i.type === 'income' ? '+' : '−' }}{{ money(i.total, 0) }}</span></td>
            </tr>
            <tr v-if="!recent.length"><td colspan="4" class="text-center text-secondary py-4">لا توجد فواتير بعد.</td></tr>
          </tbody>
        </table></div>
      </div></div>
      <div class="col-lg-5">
        <div class="panel mb-4"><div class="panel-h">أعلى الزبائن هذا الشهر</div>
          <ul class="list-group list-group-flush">
            <li v-for="c in topCustomers" :key="c.name" class="list-group-item d-flex justify-content-between"><span>{{ c.name }}</span><b>{{ money(c.total, 0) }}</b></li>
            <li v-if="!topCustomers.length" class="list-group-item text-secondary">لا توجد مبيعات هذا الشهر.</li>
          </ul></div>
        <div class="panel"><div class="panel-h">أجور المصورين هذا الشهر</div>
          <ul class="list-group list-group-flush">
            <li v-for="c in photographerCosts" :key="c.name" class="list-group-item d-flex justify-content-between"><span>{{ c.name }}</span><b>{{ money(c.total, 0) }}</b></li>
            <li v-if="!photographerCosts.length" class="list-group-item text-secondary">لا توجد أجور مسجلة هذا الشهر.</li>
          </ul></div>
      </div>
    </div>
  </AppLayout>
</template>
