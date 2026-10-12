<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import Pagination from '../../Components/Pagination.vue';
import { money } from '../../format';

defineProps({ customers: Object });
const can = computed(() => usePage().props.can);
const del = c => confirm(`حذف الزبون ${c.name}؟`) && router.delete(`/customers/${c.id}`, { preserveScroll: true });
</script>

<template>
  <AppLayout title="الزبائن">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="page-title">الزبائن</h1>
      <Link v-if="can.manage" href="/customers/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> زبون جديد</Link>
    </div>
    <div class="panel table-responsive"><table class="table mb-0 align-middle">
      <thead><tr><th>الاسم</th><th>الهاتف</th><th>الفواتير</th><th>إجمالي المبيعات</th><th></th></tr></thead>
      <tbody>
        <tr v-for="c in customers.data" :key="c.id">
          <td>{{ c.name }}</td><td>{{ c.phone }}</td><td>{{ c.invoices_count }}</td><td>{{ money(c.billed) }}</td>
          <td class="text-end text-nowrap" v-if="can.manage">
            <Link :href="`/customers/${c.id}/edit`" class="btn btn-sm btn-outline-dark"><i class="bi bi-pencil"></i></Link>
            <button class="btn btn-sm btn-outline-danger ms-1" @click="del(c)"><i class="bi bi-trash"></i></button>
          </td>
          <td v-else></td>
        </tr>
        <tr v-if="!customers.data.length"><td colspan="5" class="text-center text-secondary py-4">لا يوجد زبائن بعد.</td></tr>
      </tbody>
    </table></div>
    <Pagination :links="customers.links" />
  </AppLayout>
</template>
