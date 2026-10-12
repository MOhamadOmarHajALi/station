<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import Field from '../../Components/Field.vue';
import { cleanNum } from '../../format';

const props = defineProps({ photographer: Object });
const form = useForm({
  name: props.photographer?.name ?? '', phone: props.photographer?.phone ?? '',
  default_rate: props.photographer?.default_rate ? String(parseFloat(props.photographer.default_rate)) : '',
});
const submit = () => props.photographer ? form.put(`/photographers/${props.photographer.id}`) : form.post('/photographers');
</script>

<template>
  <AppLayout :title="photographer ? 'تعديل مصور' : 'مصور جديد'">
    <h1 class="page-title mb-3">{{ photographer ? 'تعديل مصور' : 'مصور جديد' }}</h1>
    <form @submit.prevent="submit" class="panel p-4" style="max-width:640px">
      <Field label="الاسم" :error="form.errors.name"><input v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.name }" required></Field>
      <div class="row">
        <div class="col-md-6"><Field label="الهاتف"><input v-model="form.phone" class="form-control"></Field></div>
        <div class="col-md-6"><Field label="الأجرة الافتراضية" :error="form.errors.default_rate">
          <input :value="form.default_rate" @input="form.default_rate = cleanNum($event.target.value); $event.target.value = form.default_rate" inputmode="decimal" dir="ltr" class="form-control" placeholder="0">
        </Field></div>
      </div>
      <button class="btn btn-primary" :disabled="form.processing">حفظ</button>
      <Link href="/photographers" class="btn btn-link">إلغاء</Link>
    </form>
  </AppLayout>
</template>
