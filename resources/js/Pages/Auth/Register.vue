<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Field from '../../Components/Field.vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
  <Head title="إنشاء حساب" />
  <div class="guest">
    <div class="guest-art"><div>
      <img src="/images/station-logo.png" alt="Station">
      <p class="fs-5 mt-4 mb-0" style="color:#BCD3E5">حسابات مكتب التصوير في مكان واحد</p>
    </div></div>
    <div class="guest-form"><div class="w-100" style="max-width:380px">
      <div class="text-center mb-4 d-lg-none"><span class="guest-logo"><img src="/images/station-mark.png" alt="Station" height="56"></span></div>
      <h1 class="h4 fw-bold mb-4">إنشاء حساب</h1>
      <form @submit.prevent="submit">
        <Field label="الاسم" :error="form.errors.name"><input v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.name }" required autofocus></Field>
        <Field label="البريد الإلكتروني" :error="form.errors.email"><input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" required></Field>
        <Field label="كلمة المرور" :error="form.errors.password"><input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" required></Field>
        <Field label="تأكيد كلمة المرور" :error="form.errors.password_confirmation"><input v-model="form.password_confirmation" type="password" class="form-control" required></Field>
        <button class="btn btn-primary w-100" :disabled="form.processing">
          <span v-if="form.processing" class="spinner-border spinner-border-sm"></span> إنشاء الحساب
        </button>
        <p class="text-center mt-3 mb-0">لديك حساب؟ <Link href="/login">تسجيل الدخول</Link></p>
      </form>
    </div></div>
  </div>
</template>
