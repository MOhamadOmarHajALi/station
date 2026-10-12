<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';

const props = defineProps({ customer: Object });
const form = useForm({
  name: props.customer?.name ?? '', phone: props.customer?.phone ?? '',
  email: props.customer?.email ?? '', notes: props.customer?.notes ?? '',
});
const submit = () => props.customer ? form.put(`/customers/${props.customer.id}`) : form.post('/customers');
</script>

<template>
  <AppLayout :title="customer ? 'تعديل زبون' : 'زبون جديد'">
    <h1 class="page-title mb-3">{{ customer ? 'تعديل زبون' : 'زبون جديد' }}</h1>
    <form @submit.prevent="submit" class="panel p-4" style="max-width:640px">
      <Field label="الاسم" :error="form.errors.name"><input v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.name }" required></Field>
      <div class="row">
        <div class="col-md-6"><Field label="الهاتف" :error="form.errors.phone"><input v-model="form.phone" class="form-control"></Field></div>
        <div class="col-md-6"><Field label="البريد الإلكتروني" :error="form.errors.email"><input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }"></Field></div>
      </div>
      <Field label="ملاحظات"><textarea v-model="form.notes" rows="3" class="form-control"></textarea></Field>
      <button class="btn btn-primary" :disabled="form.processing">حفظ</button>
      <Link href="/customers" class="btn btn-link">إلغاء</Link>
    </form>
  </AppLayout>
</template>
