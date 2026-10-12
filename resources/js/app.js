import './bootstrap';
import 'bootstrap/dist/css/bootstrap.rtl.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/station.css';
import 'bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { toast } from './toast';

createInertiaApp({
  title: t => (t ? `${t} — Station` : 'Station'),
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) }).use(plugin);

    // أي خطأ غير متوقع في الواجهة
    app.config.errorHandler = err => {
      console.error(err);
      toast.error('حدث خطأ غير متوقع في الواجهة.');
    };
    app.mount(el);
  },
  progress: { color: '#E8A33D' },
});

// أخطاء الشبكة والاستجابات غير الصالحة
router.on('exception', e => {
  e.preventDefault?.();
  toast.error('تعذّر الاتصال بالخادم. تحقق من الإنترنت وحاول مجددًا.');
});
router.on('invalid', e => {
  e.preventDefault();
  toast.error('استجابة غير متوقعة من الخادم.');
});
