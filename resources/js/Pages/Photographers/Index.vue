<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import Pagination from '../../Components/Pagination.vue';
import { money } from '../../format';

defineProps({ photographers: Object });
const can = computed(() => usePage().props.can);
const del = p => confirm(`حذف المصور ${p.name}؟`) && router.delete(`/photographers/${p.id}`, { preserveScroll: true });
</script>

<template>
  <AppLayout title="المصورون">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="page-title">المصورون</h1>
      <Link v-if="can.manage" href="/photographers/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> مصور جديد</Link>
    </div>
    <div class="panel table-responsive"><table class="table mb-0 align-middle">
      <thead><tr><th>الاسم</th><th>الهاتف</th><th>الأجرة الافتراضية</th><th>إجمالي ما دُفع له</th><th></th></tr></thead>
      <tbody>
        <tr v-for="p in photographers.data" :key="p.id">
          <td>{{ p.name }}</td><td>{{ p.phone }}</td><td>{{ p.default_rate ? money(p.default_rate) : '—' }}</td><td>{{ money(p.paid) }}</td>
          <td class="text-end text-nowrap" v-if="can.manage">
            <Link :href="`/invoices/create?type=expense&photographer=${p.id}`" class="btn btn-sm btn-primary">فاتورة أجر</Link>
            <Link :href="`/photographers/${p.id}/edit`" class="btn btn-sm btn-outline-dark ms-1"><i class="bi bi-pencil"></i></Link>
            <button class="btn btn-sm btn-outline-danger ms-1" @click="del(p)"><i class="bi bi-trash"></i></button>
          </td>
          <td v-else></td>
        </tr>
        <tr v-if="!photographers.data.length"><td colspan="5" class="text-center text-secondary py-4">لا يوجد مصورون بعد.</td></tr>
      </tbody>
    </table></div>
    <Pagination :links="photographers.links" />
  </AppLayout>
</template>
