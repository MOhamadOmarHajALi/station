<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { money } from '../../format';

const p = defineProps({ month: String, monthLabel: String, income: Number, expense: Number, profit: Number,
  partners: Array, incomeInvoices: Array, expenseInvoices: Array });
const cur = usePage().props.currency;
const m = ref(p.month);
const go = () => router.get('/reports/monthly', { month: m.value });
</script>

<template>
  <AppLayout title="التقرير الشهري">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <h1 class="page-title">التقرير الشهري — {{ monthLabel }}</h1>
      <div class="d-flex gap-2 d-print-none">
        <input type="month" v-model="m" class="form-control" style="width:auto" @change="go">
        <button class="btn btn-outline-dark" onclick="window.print()"><i class="bi bi-printer"></i></button>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">الواردات</div><div class="fs-3 fw-bold" style="color:var(--teal)">{{ money(income) }}</div></div></div>
      <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">الصادرات</div><div class="fs-3 fw-bold" style="color:var(--red)">{{ money(expense) }}</div></div></div>
      <div class="col-md-4"><div class="panel p-3"><div class="text-secondary">صافي الربح</div><div class="fs-3 fw-bold">{{ money(profit) }} <small class="fs-6">{{ cur }}</small></div></div></div>
    </div>

    <div class="panel mb-4">
      <div class="panel-h">توزيع الأرباح</div>
      <table class="table mb-0"><thead><tr><th>الشريك</th><th>النسبة</th><th>المبلغ</th></tr></thead>
        <tbody><tr v-for="x in partners" :key="x.name"><td>{{ x.name }}</td><td>{{ x.percent }}%</td><td>{{ money(x.amount) }}</td></tr></tbody></table>
    </div>

    <div class="row g-4">
      <div class="col-lg-6"><div class="panel"><div class="panel-h">فواتير الواردات</div>
        <table class="table table-sm mb-0"><tbody>
          <tr v-for="i in incomeInvoices" :key="i.id"><td><Link :href="`/invoices/${i.id}`">{{ i.number }}</Link></td><td>{{ i.party }}</td><td>{{ i.date }}</td><td>{{ money(i.total) }}</td></tr>
          <tr v-if="!incomeInvoices.length"><td class="text-secondary p-3">لا توجد واردات في هذا الشهر.</td></tr>
        </tbody></table></div></div>
      <div class="col-lg-6"><div class="panel"><div class="panel-h">فواتير الصادرات</div>
        <table class="table table-sm mb-0"><tbody>
          <tr v-for="i in expenseInvoices" :key="i.id"><td><Link :href="`/invoices/${i.id}`">{{ i.number }}</Link></td><td>{{ i.party }}</td><td>{{ i.date }}</td><td>{{ money(i.total) }}</td></tr>
          <tr v-if="!expenseInvoices.length"><td class="text-secondary p-3">لا توجد صادرات في هذا الشهر.</td></tr>
        </tbody></table></div></div>
    </div>
  </AppLayout>
</template>
