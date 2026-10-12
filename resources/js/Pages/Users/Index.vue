<script setup>
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({ users: Array });
const me = usePage().props.auth.user.id;
const setRole = (u, role) => router.patch(`/users/${u.id}`, { role }, { preserveScroll: true });
</script>

<template>
  <AppLayout title="المستخدمون والصلاحيات">
    <h1 class="page-title mb-1">المستخدمون والصلاحيات</h1>
    <p class="text-secondary mb-4">
      <b>مدير:</b> كل شيء + إدارة المستخدمين · <b>محاسب:</b> إضافة وتعديل وحذف البيانات · <b>مشاهد:</b> عرض فقط.
      أول حساب يُسجَّل في النظام يصبح مديرًا تلقائيًا.
    </p>
    <div class="panel table-responsive"><table class="table mb-0 align-middle">
      <thead><tr><th>الاسم</th><th>البريد</th><th style="width:200px">الصلاحية</th></tr></thead>
      <tbody><tr v-for="u in users" :key="u.id">
        <td>{{ u.name }} <span v-if="u.id === me" class="badge bg-secondary">أنت</span></td><td>{{ u.email }}</td>
        <td><select class="form-select form-select-sm" :value="u.role" :disabled="u.id === me" @change="setRole(u, $event.target.value)">
          <option value="admin">مدير</option><option value="accountant">محاسب</option><option value="viewer">مشاهد</option>
        </select></td>
      </tr></tbody>
    </table></div>
  </AppLayout>
</template>
