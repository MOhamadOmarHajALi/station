<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Field from '../../Components/Field.vue';

defineProps({ status: String });
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post('/login', { onFinish: () => form.reset('password') });
</script>

<template>
  <Head title="تسجيل الدخول" />
  <div class="guest">
    <div class="guest-art"><div>
      <img src="/images/station-logo.png" alt="Station">
      <p class="fs-5 mt-4 mb-0" style="color:#BCD3E5">حسابات مكتب التصوير في مكان واحد</p>
    </div></div>
    <div class="guest-form"><div class="w-100" style="max-width:380px">
      <div class="text-center mb-4 d-lg-none"><span class="guest-logo"><img src="/images/station-mark.png" alt="Station" height="56"></span></div>
      <h1 class="h4 fw-bold mb-4">تسجيل الدخول</h1>
      <div v-if="status" class="alert alert-info py-2">{{ status }}</div>
      <form @submit.prevent="submit">
        <Field label="البريد الإلكتروني" :error="form.errors.email">
          <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" required autofocus>
        </Field>
        <Field label="كلمة المرور" :error="form.errors.password">
          <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" required>
        </Field>
        <div class="form-check mb-3">
          <input v-model="form.remember" class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">تذكرني</label>
        </div>
        <button class="btn btn-primary w-100" :disabled="form.processing">
          <span v-if="form.processing" class="spinner-border spinner-border-sm"></span> دخول
        </button>
        <p class="text-center mt-3 mb-0">ليس لديك حساب؟ <Link href="/register">إنشاء حساب</Link></p>
      </form>
    </div></div>
  </div>
</template>
