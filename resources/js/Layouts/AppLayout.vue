<script setup>
import { Link, router, usePage, Head } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted } from 'vue';
import { toasts, toast } from '../toast';
import { roleLabel } from '../format';

defineProps({ title: String });

const page = usePage();
const open = ref(false);
const url = computed(() => page.url);
const can = computed(() => page.props.can);
const user = computed(() => page.props.auth.user);

const isExpense = computed(() => url.value.includes('type=expense'));
const nav = computed(() => [
  { href: '/dashboard', icon: 'bi-grid-1x2', label: 'لوحة التحكم', active: url.value.startsWith('/dashboard') },
  { href: '/invoices?type=income', icon: 'bi-box-arrow-in-down', label: 'الواردات', active: url.value.startsWith('/invoices') && !isExpense.value },
  { href: '/invoices?type=expense', icon: 'bi-box-arrow-up', label: 'الصادرات', active: url.value.startsWith('/invoices') && isExpense.value },
  { href: '/recurring', icon: 'bi-arrow-repeat', label: 'الفواتير التلقائية', active: url.value.startsWith('/recurring') },
  { href: '/customers', icon: 'bi-people', label: 'الزبائن', active: url.value.startsWith('/customers') },
  { href: '/photographers', icon: 'bi-camera', label: 'المصورون', active: url.value.startsWith('/photographers') },
  { href: '/reports/monthly', icon: 'bi-calendar3', label: 'التقرير الشهري', active: url.value.startsWith('/reports') },
  ...(can.value.admin ? [{ href: '/users', icon: 'bi-shield-lock', label: 'المستخدمون والصلاحيات', active: url.value.startsWith('/users') }] : []),
]);

// رسائل النجاح/الخطأ القادمة من الخادم
function showFlash() {
  const f = page.props.flash || {};
  if (f.success) toast.success(f.success);
  if (f.error) toast.error(f.error);
}
onMounted(showFlash);
watch(() => page.props.flash, showFlash, { deep: true });

router.on('navigate', () => (open.value = false));
const logout = () => router.post('/logout');
</script>

<template>
  <Head :title="title" />
  <div class="shell">
    <div v-if="open" class="scrim d-lg-none" @click="open = false"></div>
    <aside class="sidebar" :class="{ open }">
      <div class="side-inner">
        <div class="brand">
          <img src="/images/station-mark.png" class="brand-mark" alt="">
          <div><b>Station</b><small>مكتب تصوير</small></div>
        </div>
        <nav class="side-nav">
          <Link v-for="n in nav" :key="n.href" :href="n.href" :class="{ active: n.active }">
            <i class="bi" :class="n.icon"></i> {{ n.label }}
          </Link>
        </nav>
        <div class="side-foot">
          <div class="mb-2">{{ user.name }} <span class="badge bg-light text-dark ms-1">{{ roleLabel(user.role) }}</span></div>
          <button class="btn btn-sm btn-outline-light w-100" @click="logout"><i class="bi bi-box-arrow-left"></i> تسجيل الخروج</button>
        </div>
      </div>
    </aside>

    <main class="main">
      <div class="d-lg-none mb-3 d-flex justify-content-between align-items-center">
        <b class="fs-5">Station</b>
        <button class="btn btn-outline-dark" @click="open = true" aria-label="القائمة"><i class="bi bi-list"></i></button>
      </div>
      <slot />
    </main>

    <div class="toasts">
      <div v-for="t in toasts" :key="t.id" class="alert py-2 mb-0 shadow-sm toast-in" :class="'alert-' + t.type">{{ t.msg }}</div>
    </div>
  </div>
</template>
