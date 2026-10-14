<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import Pagination from '../../Components/Pagination.vue';
import { money } from '../../format';

const props = defineProps({ type: String, grand: Number, invoices: Object });
const page = usePage();
const can = computed(() => page.props.can);
const isIn = computed(() => props.type === 'income');
const del = i => confirm(`حذف الفاتورة ${i.number}؟`) && router.delete(`/invoices/${i.id}`, { preserveScroll: true });
</script>

<template>
  <AppLayout :title="isIn ? 'الواردات' : 'الصادرات'">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h1 class="page-title">{{ isIn ? 'الواردات' : 'الصادرات' }}</h1>
        <div class="text-secondary">الإجمالي: <b>{{ money(grand) }}</b> {{ page.props.currency }}</div>
      </div>
      <Link v-if="can.manage" :href="`/invoices/create?type=${type}`" class="btn btn-primary"><i class="bi bi-plus-lg"></i> {{ isIn ? 'فاتورة وارد' : 'فاتورة صادر' }}</Link>
    </div>
    <div class="panel table-responsive"><table class="table mb-0 align-middle">
      <thead><tr><th>الرقم</th><th>{{ isIn ? 'الزبون' : 'المصور / البيان' }}</th><th>التاريخ</th><th>المبلغ</th><th></th></tr></thead>
      <tbody>
        <tr v-for="i in invoices.data" :key="i.id">
          <td><Link :href="`/invoices/${i.id}`">{{ i.number }}</Link></td>
          <td>{{ isIn ? i.customer?.name : (i.photographer?.name ?? i.title) }}</td>
          <td>{{ String(i.invoice_date).slice(0, 10) }}</td>
          <td>{{ money(i.total) }}</td>
          <td class="text-end text-nowrap">
            <template v-if="can.manage">
              <Link :href="`/invoices/${i.id}/edit`" class="btn btn-sm btn-outline-dark"><i class="bi bi-pencil"></i></Link>
              <button class="btn btn-sm btn-outline-danger ms-1" @click="del(i)"><i class="bi bi-trash"></i></button>
            </template>
          </td>
        </tr>
        <tr v-if="!invoices.data.length"><td colspan="5" class="text-center text-secondary py-4">لا توجد فواتير بعد.</td></tr>
      </tbody>
    </table></div>
    <Pagination :links="invoices.links" />
  </AppLayout>
</template>
