<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { money } from '../../format';

const p = defineProps({ invoice: Object });
const cur = usePage().props.currency;
const isIn = p.invoice.type === 'income';
</script>

<template>
  <AppLayout :title="invoice.number">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 d-print-none">
      <h1 class="page-title">{{ invoice.number }} <span class="badge fs-6" :class="isIn ? 'badge-in' : 'badge-ex'">{{ isIn ? 'وارد' : 'صادر' }}</span></h1>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> طباعة</button>
        <Link :href="`/invoices?type=${invoice.type}`" class="btn btn-outline-dark">القائمة</Link>
      </div>
    </div>

    <div class="panel p-4">
      <div class="d-flex justify-content-between align-items-center border-bottom border-2 pb-3 mb-3" style="border-color:#174E77!important">
        <div class="d-flex align-items-center gap-3">
          <div style="background:#174E77;padding:10px 14px;border-radius:8px;print-color-adjust:exact;-webkit-print-color-adjust:exact"><img src="/images/station-mark.png" height="52" alt=""></div>
          <div><div class="h3 fw-bold mb-0">Station</div><div class="text-secondary">مكتب تصوير</div></div>
        </div>
        <div class="text-start"><div class="fw-bold fs-5">{{ isIn ? 'فاتورة مبيعات' : 'فاتورة مصروف' }}</div>
          <div>رقم: {{ invoice.number }}</div><div>التاريخ: {{ String(invoice.invoice_date).slice(0, 10) }}</div></div>
      </div>
      <p class="fs-5">{{ isIn ? 'الزبون: ' + (invoice.customer?.name ?? '') : 'المستفيد: ' + (invoice.photographer?.name ?? invoice.title ?? '—') }}</p>
      <div class="table-responsive"><table class="table">
        <thead><tr><th>الوصف</th><th>الكمية</th><th>السعر</th><th>المجموع</th></tr></thead>
        <tbody><tr v-for="it in invoice.items" :key="it.id">
          <td>{{ it.description }}</td><td>{{ parseFloat(it.quantity) }}</td><td>{{ money(it.price) }}</td><td>{{ money(it.subtotal) }}</td></tr></tbody>
        <tfoot><tr><th colspan="3" class="text-end">الإجمالي</th><th>{{ money(invoice.total) }} {{ cur }}</th></tr></tfoot>
      </table></div>
      <div v-if="invoice.notes" class="text-secondary">ملاحظات: {{ invoice.notes }}</div>
    </div>
  </AppLayout>
</template>
