<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Pagination from '../../Components/Pagination.vue';

defineProps({ messages: Object });
const toggle = m => router.patch(`/messages/${m.id}`, {}, { preserveScroll: true });
const del = m => confirm('حذف هذه الرسالة؟') && router.delete(`/messages/${m.id}`, { preserveScroll: true });
const when = d => new Date(d).toLocaleString('ar', { dateStyle: 'medium', timeStyle: 'short' });
</script>

<template>
  <AppLayout title="الرسائل">
    <h1 class="page-title mb-1">رسائل الموقع</h1>
    <p class="text-secondary mb-4">الرسائل التي يرسلها الزوار من نموذج "تواصل معنا" في صفحة الموقع.</p>

    <div v-for="m in messages.data" :key="m.id" class="panel mb-3" :style="!m.read_at ? 'border-right:4px solid var(--amber)' : ''">
      <div class="panel-h">
        <div><b>{{ m.name }}</b> <span v-if="!m.read_at" class="badge bg-warning text-dark ms-1">جديدة</span>
          <span v-if="m.service" class="badge bg-light text-dark ms-1">{{ m.service }}</span></div>
        <small class="text-secondary fw-normal">{{ when(m.created_at) }}</small>
      </div>
      <div class="p-3">
        <p class="mb-2" style="white-space:pre-line">{{ m.message }}</p>
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <a :href="`tel:${m.phone}`" class="btn btn-sm btn-outline-dark"><i class="bi bi-telephone"></i> {{ m.phone }}</a>
          <a v-if="m.email" :href="`mailto:${m.email}`" class="btn btn-sm btn-outline-dark"><i class="bi bi-envelope"></i> {{ m.email }}</a>
          <span class="ms-auto"></span>
          <button class="btn btn-sm btn-outline-secondary" @click="toggle(m)">{{ m.read_at ? 'تحديد كغير مقروءة' : 'تحديد كمقروءة' }}</button>
          <button class="btn btn-sm btn-outline-danger" @click="del(m)"><i class="bi bi-trash"></i></button>
        </div>
      </div>
    </div>
    <div v-if="!messages.data.length" class="panel p-5 text-center text-secondary">لا توجد رسائل بعد.</div>
    <Pagination :links="messages.links" />
  </AppLayout>
</template>
