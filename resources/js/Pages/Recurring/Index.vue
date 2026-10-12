<script setup>
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';
import { money, cleanNum } from '../../format';

defineProps({ items: Array, customers: Array, photographers: Array });
const page = usePage();
const can = computed(() => page.props.can);

const form = useForm({ type: 'income', title: '', amount: '', day_of_month: '1', customer_id: '', photographer_id: '' });
const submit = () => form.transform(d => ({ ...d, amount: parseFloat(d.amount) || '', day_of_month: parseInt(d.day_of_month) || '' }))
  .post('/recurring', { preserveScroll: true, onSuccess: () => form.reset() });

const toggle = r => router.patch(`/recurring/${r.id}`, { active: !r.active }, { preserveScroll: true });
const del = r => confirm(`حذف "${r.title}"؟`) && router.delete(`/recurring/${r.id}`, { preserveScroll: true });
const run = () => router.post('/recurring/run', {}, { preserveScroll: true });
</script>

<template>
  <AppLayout title="الفواتير التلقائية">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
      <h1 class="page-title">الفواتير التلقائية</h1>
      <button v-if="can.manage" class="btn btn-outline-dark" @click="run"><i class="bi bi-play-fill"></i> تشغيل الآن</button>
    </div>
    <p class="text-secondary mb-4">فواتير تتكرر كل شهر (إيجار، اشتراك زبون ثابت، راتب مصور). يُنشئها النظام تلقائيًا في اليوم المحدد دون تدخل منك.</p>

    <form v-if="can.manage" @submit.prevent="submit" class="panel p-3 mb-4">
      <div class="row g-2 align-items-start">
        <div class="col-md-2"><Field label="النوع"><select v-model="form.type" class="form-select"><option value="income">وارد</option><option value="expense">صادر</option></select></Field></div>
        <div class="col-md-3"><Field label="العنوان" :error="form.errors.title"><input v-model="form.title" class="form-control" :class="{ 'is-invalid': form.errors.title }" placeholder="إيجار المكتب"></Field></div>
        <div class="col-md-2"><Field label="المبلغ" :error="form.errors.amount"><input :value="form.amount" @input="form.amount = cleanNum($event.target.value); $event.target.value = form.amount" inputmode="decimal" dir="ltr" class="form-control" :class="{ 'is-invalid': form.errors.amount }"></Field></div>
        <div class="col-md-1"><Field label="اليوم" :error="form.errors.day_of_month"><input :value="form.day_of_month" @input="form.day_of_month = cleanNum($event.target.value).split('.')[0]; $event.target.value = form.day_of_month" inputmode="numeric" dir="ltr" class="form-control"></Field></div>
        <div class="col-md-3">
          <Field v-if="form.type === 'income'" label="الزبون" :error="form.errors.customer_id"><select v-model="form.customer_id" class="form-select" :class="{ 'is-invalid': form.errors.customer_id }"><option value="">— اختر —</option><option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option></select></Field>
          <Field v-else label="المصور (اختياري)"><select v-model="form.photographer_id" class="form-select"><option value="">—</option><option v-for="p in photographers" :key="p.id" :value="p.id">{{ p.name }}</option></select></Field>
        </div>
        <div class="col-md-1 pt-4"><button class="btn btn-primary w-100 mt-1" :disabled="form.processing">إضافة</button></div>
      </div>
    </form>

    <div class="panel table-responsive"><table class="table mb-0 align-middle">
      <thead><tr><th>العنوان</th><th>النوع</th><th>الجهة</th><th>المبلغ</th><th>اليوم</th><th>آخر تشغيل</th><th>الحالة</th><th></th></tr></thead>
      <tbody>
        <tr v-for="r in items" :key="r.id" :class="{ 'text-secondary': !r.active }">
          <td>{{ r.title }}</td>
          <td><span class="badge" :class="r.type === 'income' ? 'badge-in' : 'badge-ex'">{{ r.type === 'income' ? 'وارد' : 'صادر' }}</span></td>
          <td>{{ r.customer?.name ?? r.photographer?.name ?? '—' }}</td>
          <td>{{ money(r.amount) }}</td><td>{{ r.day_of_month }}</td>
          <td>{{ r.last_run_on ? String(r.last_run_on).slice(0, 10) : '—' }}</td>
          <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" :checked="r.active" :disabled="!can.manage" @change="toggle(r)"></div></td>
          <td class="text-end"><button v-if="can.manage" class="btn btn-sm btn-outline-danger" @click="del(r)"><i class="bi bi-trash"></i></button></td>
        </tr>
        <tr v-if="!items.length"><td colspan="8" class="text-center text-secondary py-4">لا توجد فواتير تلقائية بعد.</td></tr>
      </tbody>
    </table></div>
  </AppLayout>
</template>
