<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ status: Number });
const msg = computed(() => ({
  403: ['ليست لديك صلاحية', 'هذه الصفحة أو العملية غير مسموحة لحسابك. اطلب من المدير تغيير صلاحيتك.'],
  404: ['الصفحة غير موجودة', 'ربما حُذف الرابط أو كُتب بشكل خاطئ.'],
  419: ['انتهت الجلسة', 'أعد تحميل الصفحة وحاول مرة أخرى.'],
  500: ['خطأ في الخادم', 'حدث خطأ غير متوقع. تم تسجيله وسنراجعه، حاول لاحقًا.'],
  503: ['الخدمة متوقفة مؤقتًا', 'النظام تحت الصيانة، عد بعد قليل.'],
}[props.status] || ['حدث خطأ', 'حاول مرة أخرى.']));
</script>

<template>
  <Head :title="msg[0]" />
  <div class="d-flex align-items-center justify-content-center text-center p-4" style="min-height:100vh">
    <div style="max-width:440px">
      <span class="guest-logo mb-4"><img src="/images/station-mark.png" height="56" alt=""></span>
      <div class="display-3 fw-bold" style="color:var(--ink)">{{ status }}</div>
      <h1 class="h4 mt-2">{{ msg[0] }}</h1>
      <p class="text-secondary">{{ msg[1] }}</p>
      <div class="d-flex gap-2 justify-content-center">
        <Link href="/" class="btn btn-primary">الرئيسية</Link>
        <button class="btn btn-outline-dark" onclick="history.back()">رجوع</button>
      </div>
    </div>
  </div>
</template>
