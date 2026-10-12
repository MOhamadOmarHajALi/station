<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';
import { money, cleanNum } from '../../format';

const props = defineProps({
  type: String, number: String, today: String, customers: Array, photographers: Array, selectedPhotographer: Number,
});
const isIn = props.type === 'income';
const rateOf = id => parseFloat(props.photographers.find(p => p.id === Number(id))?.default_rate) || '';

const form = useForm({
  type: props.type, invoice_date: props.today, customer_id: '', photographer_id: props.selectedPhotographer ?? '',
  title: '', notes: '',
  items: [{ description: props.selectedPhotographer ? 'أجرة تصوير' : '', quantity: '1', price: String(rateOf(props.selectedPhotographer)) }],
});

const addRow = () => form.items.push({ description: '', quantity: '1', price: '' });
const removeRow = i => form.items.length > 1 && form.items.splice(i, 1);
const num = (row, k, e) => { row[k] = cleanNum(e.target.value); e.target.value = row[k]; };
const sub = r => (parseFloat(r.quantity) || 0) * (parseFloat(r.price) || 0);
const total = computed(() => form.items.reduce((s, r) => s + sub(r), 0));

// اختيار مصور يملأ أجرته الافتراضية في البند الأول
watch(() => form.photographer_id, id => {
  const r = rateOf(id), first = form.items[0];
  if (r && first) { if (!first.description) first.description = 'أجرة تصوير'; first.price = String(r); }
});

const err = (i, k) => form.errors[`items.${i}.${k}`];
const submit = () => form
  .transform(d => ({ ...d, items: d.items.map(r => ({ ...r, quantity: parseFloat(r.quantity) || 0, price: r.price === '' ? '' : parseFloat(r.price) })) }))
  .post('/invoices');
</script>

<template>
  <AppLayout :title="isIn ? 'فاتورة وارد' : 'فاتورة صادر'">
    <h1 class="page-title mb-1">{{ isIn ? 'فاتورة وارد' : 'فاتورة صادر' }}</h1>
    <div class="text-secondary mb-3">رقم الفاتورة: {{ number }}</div>

    <form @submit.prevent="submit" class="panel p-4">
      <div class="row g-3 mb-3">
        <div class="col-md-3"><Field label="التاريخ" :error="form.errors.invoice_date"><input type="date" v-model="form.invoice_date" class="form-control" required></Field></div>
        <template v-if="isIn">
          <div class="col-md-5"><Field label="الزبون" :error="form.errors.customer_id">
            <select v-model="form.customer_id" class="form-select" :class="{ 'is-invalid': form.errors.customer_id }">
              <option value="">— اختر الزبون —</option>
              <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select></Field></div>
        </template>
        <template v-else>
          <div class="col-md-4"><Field label="المصور"><select v-model="form.photographer_id" class="form-select">
            <option value="">— بدون مصور —</option>
            <option v-for="p in photographers" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select></Field></div>
          <div class="col-md-5"><Field label="البيان (للمصاريف الأخرى)"><input v-model="form.title" class="form-control" placeholder="مثال: إيجار المكتب"></Field></div>
        </template>
      </div>

      <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>الوصف</th><th style="width:110px">الكمية</th><th style="width:150px">السعر</th><th style="width:140px">المجموع</th><th style="width:50px"></th></tr></thead>
        <tbody>
          <tr v-for="(r, i) in form.items" :key="i">
            <td><input v-model="r.description" class="form-control" :class="{ 'is-invalid': err(i, 'description') }" required>
              <div v-if="err(i, 'description')" class="text-danger small">{{ err(i, 'description') }}</div></td>
            <td><input :value="r.quantity" @input="num(r, 'quantity', $event)" @blur="!parseFloat(r.quantity) && (r.quantity = '1')" @focus="$event.target.select()" inputmode="decimal" dir="ltr" class="form-control"></td>
            <td><input :value="r.price" @input="num(r, 'price', $event)" @focus="$event.target.select()" inputmode="decimal" dir="ltr" placeholder="0" class="form-control" :class="{ 'is-invalid': err(i, 'price') }">
              <div v-if="err(i, 'price')" class="text-danger small">{{ err(i, 'price') }}</div></td>
            <td>{{ money(sub(r)) }}</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger" @click="removeRow(i)" :disabled="form.items.length === 1">×</button></td>
          </tr>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">الإجمالي</th><th>{{ money(total) }}</th><th></th></tr></tfoot>
      </table></div>
      <div v-if="form.errors.items" class="text-danger small mb-2">{{ form.errors.items }}</div>
      <button type="button" class="btn btn-outline-dark mb-3" @click="addRow"><i class="bi bi-plus"></i> إضافة بند</button>

      <Field label="ملاحظات"><textarea v-model="form.notes" rows="2" class="form-control"></textarea></Field>
      <button class="btn btn-primary" :disabled="form.processing"><span v-if="form.processing" class="spinner-border spinner-border-sm"></span> حفظ الفاتورة</button>
      <Link :href="`/invoices?type=${type}`" class="btn btn-link">إلغاء</Link>
    </form>
  </AppLayout>
</template>
