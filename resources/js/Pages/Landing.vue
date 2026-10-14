<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

/* ================= ✏️ عدّل هذا القسم ببيانات مكتبك ================= */
const site = {
  name: 'Station',
  tagline: 'مكتب تصوير',
  phone: '0000 000 0000',
  whatsapp: '',                       // رقم واتساب بالصيغة الدولية بدون + مثل 9647700000000 (اتركه فارغًا لإخفاء الزر)
  email: 'info@station.example',
  address: 'اكتب عنوان المكتب هنا',
  hours: 'يوميًا من 10 صباحًا حتى 9 مساءً',
  socials: [
    { icon: 'bi-instagram', href: '#', label: 'Instagram' },
    { icon: 'bi-facebook', href: '#', label: 'Facebook' },
    { icon: 'bi-tiktok', href: '#', label: 'TikTok' },
    { icon: 'bi-youtube', href: '#', label: 'YouTube' },
  ],
};
const stats = [
  { n: 8, suffix: '+', l: 'سنوات خبرة' },
  { n: 1200, suffix: '+', l: 'جلسة تصوير' },
  { n: 950, suffix: '+', l: 'عميل سعيد' },
  { n: 40, suffix: '+', l: 'فعالية وحفل شهريًا' },
];
// الصور تُدار من لوحة التحكم (صور الموقع). إن لم تُضف صور بعد تظهر هذه الصور المؤقتة
const props = defineProps({ works: { type: Array, default: () => [] } });
const placeholders = [
  { title: 'حفل زفاف', cat: 'wedding', src: '/images/portfolio/work-1.svg' },
  { title: 'جلسة بورتريه', cat: 'portrait', src: '/images/portfolio/work-2.svg' },
  { title: 'تصوير منتجات', cat: 'product', src: '/images/portfolio/work-3.svg' },
  { title: 'حفل خطوبة', cat: 'wedding', src: '/images/portfolio/work-4.svg' },
  { title: 'مؤتمر شركة', cat: 'event', src: '/images/portfolio/work-5.svg' },
  { title: 'جلسة عائلية', cat: 'portrait', src: '/images/portfolio/work-6.svg' },
  { title: 'عروس وعريس', cat: 'wedding', src: '/images/portfolio/work-7.svg' },
  { title: 'افتتاح معرض', cat: 'event', src: '/images/portfolio/work-8.svg' },
  { title: 'منتجات متجر', cat: 'product', src: '/images/portfolio/work-9.svg' },
];
const gallery = computed(() => (props.works.length ? props.works : placeholders));
const hero = i => gallery.value[i % gallery.value.length].src;
/* =================================================================== */

const cats = [
  { k: 'all', t: 'الكل' }, { k: 'wedding', t: 'أعراس' }, { k: 'portrait', t: 'بورتريه وعائلي' },
  { k: 'product', t: 'منتجات' }, { k: 'event', t: 'فعاليات' },
];
const services = [
  { icon: 'bi-heart', t: 'تصوير الأعراس والحفلات', d: 'نوثّق أجمل لحظات يومكم بتفاصيلها وعفويتها، من التحضيرات حتى آخر الحفل.' },
  { icon: 'bi-person-bounding-box', t: 'بورتريه وجلسات عائلية', d: 'جلسات في الاستوديو أو في الهواء الطلق بإضاءة احترافية وتوجيه مريح.' },
  { icon: 'bi-box-seam', t: 'تصوير المنتجات', d: 'صور نظيفة وجذابة لمتجرك ومنصاتك تزيد ثقة العميل وتدعم مبيعاتك.' },
  { icon: 'bi-building', t: 'فعاليات وشركات', d: 'مؤتمرات، افتتاحات، وفعاليات مؤسسية بتغطية كاملة وتسليم سريع.' },
  { icon: 'bi-film', t: 'فيديو وريلز', d: 'مقاطع قصيرة ومونتاج احترافي لمنصات التواصل والإعلانات.' },
  { icon: 'bi-journal-richtext', t: 'ألبومات وطباعة', d: 'ألبومات فاخرة ولوحات مطبوعة بجودة عالية تبقى لسنوات.' },
];
const steps = [
  { n: '01', t: 'تواصل معنا', d: 'أخبرنا بنوع الجلسة وموعدها وما تتخيله.' },
  { n: '02', t: 'نخطّط معًا', d: 'نتفق على الفكرة والموقع والباقة المناسبة.' },
  { n: '03', t: 'يوم التصوير', d: 'فريقنا يعمل بهدوء واحتراف لتخرج الصور طبيعية.' },
  { n: '04', t: 'التسليم', d: 'نعدّل الصور ونسلّمها لك في الموعد المتفق عليه.' },
];
const links = [
  { id: 'home', t: 'الرئيسية' }, { id: 'services', t: 'خدماتنا' }, { id: 'work', t: 'أعمالنا' },
  { id: 'process', t: 'كيف نعمل' }, { id: 'contact', t: 'تواصل معنا' },
];

/* ---------- حالة ---------- */
const scrolled = ref(false);
const menu = ref(false);
const active = ref('home');
const cat = ref('all');
const lb = ref(-1); // فهرس الصورة المفتوحة في المعرض المكبّر
const filtered = computed(() => gallery.value.filter(w => cat.value === 'all' || w.cat === cat.value));

const form = useForm({ name: '', phone: '', email: '', service: '', message: '', website: '' });
const sent = ref(false);
const send = () => form.post('/contact', {
  preserveScroll: true,
  onSuccess: () => { sent.value = true; form.reset(); },
});

/* ---------- المعرض المكبّر ---------- */
const open = i => { lb.value = i; };
const close = () => { lb.value = -1; };
const next = () => (lb.value = (lb.value + 1) % filtered.value.length);
const prev = () => (lb.value = (lb.value - 1 + filtered.value.length) % filtered.value.length);
const onKey = e => {
  if (lb.value < 0) return;
  if (e.key === 'Escape') close();
  if (e.key === 'ArrowLeft') next();   // RTL: السهم الأيسر = التالي
  if (e.key === 'ArrowRight') prev();
};
watch(lb, v => (document.body.style.overflow = v >= 0 ? 'hidden' : ''));
watch(cat, () => close());

/* ---------- حركة ---------- */
let io, so, raf = [];
const countUp = (el, to) => {
  const t0 = performance.now(), dur = 1600;
  const tick = now => {
    const k = Math.min((now - t0) / dur, 1);
    el.textContent = Math.round(to * (1 - Math.pow(1 - k, 3))).toLocaleString('en-US');
    if (k < 1) raf.push(requestAnimationFrame(tick));
  };
  raf.push(requestAnimationFrame(tick));
};
const onScroll = () => (scrolled.value = window.scrollY > 30);

onMounted(() => {
  document.documentElement.style.scrollBehavior = 'smooth';
  onScroll();
  addEventListener('scroll', onScroll, { passive: true });
  addEventListener('keydown', onKey);

  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const rv = [...document.querySelectorAll('.land .rv')];
  if (reduce) {
    rv.forEach(e => e.classList.add('in'));
    document.querySelectorAll('.land [data-count]').forEach(c => (c.textContent = Number(c.dataset.count).toLocaleString('en-US')));
  } else {
    io = new IntersectionObserver(es => es.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.classList.add('in');
      e.target.querySelectorAll('[data-count]').forEach(c => countUp(c, Number(c.dataset.count)));
      io.unobserve(e.target);
    }), { threshold: .15 });
    rv.forEach(e => io.observe(e));
  }

  // تمييز القسم الحالي في الهيدر
  so = new IntersectionObserver(es => es.forEach(e => e.isIntersecting && (active.value = e.target.id)), { rootMargin: '-45% 0px -50% 0px' });
  links.forEach(l => { const el = document.getElementById(l.id); el && so.observe(el); });
});

onBeforeUnmount(() => {
  document.documentElement.style.scrollBehavior = '';
  document.body.style.overflow = '';
  removeEventListener('scroll', onScroll);
  removeEventListener('keydown', onKey);
  io && io.disconnect(); so && so.disconnect();
  raf.forEach(cancelAnimationFrame);
});
</script>

<template>
  <Head :title="`${site.name} — ${site.tagline}`" />
  <div class="land" id="top">
    <!-- ============ HEADER ============ -->
    <header class="nav" :class="{ solid: scrolled || menu }">
      <div class="container nav-in">
        <a href="#home" class="brand"><img src="/images/station-mark.png" alt="" height="38"><b>{{ site.name }}</b></a>

        <nav class="links d-none d-lg-flex">
          <a v-for="l in links" :key="l.id" :href="`#${l.id}`" :class="{ on: active === l.id }" @click="menu = false">{{ l.t }}</a>
        </nav>

        <div class="d-flex align-items-center gap-2">
          <a href="/login" class="btn btn-sm btn-outline-light d-none d-md-inline-flex"><i class="bi bi-box-arrow-in-left ms-1"></i> دخول النظام</a>
          <a href="#contact" class="btn btn-sm btn-warning fw-semibold d-none d-sm-inline-flex">احجز جلسة</a>
          <button class="burger d-lg-none" @click="menu = !menu" aria-label="القائمة"><i class="bi" :class="menu ? 'bi-x-lg' : 'bi-list'"></i></button>
        </div>
      </div>
      <div v-if="menu" class="mobile d-lg-none">
        <a v-for="l in links" :key="l.id" :href="`#${l.id}`" @click="menu = false">{{ l.t }}</a>
        <a href="/login" class="text-warning">دخول النظام</a>
      </div>
    </header>

    <!-- ============ HERO ============ -->
    <section id="home" class="hero">
      <div class="container hero-grid">
        <div class="hero-text">
          <span class="chip up u1"><i class="bi bi-camera-fill"></i> {{ site.tagline }}</span>
          <h1 class="up u2">نلتقط اللحظة…<br><span>ونحوّلها إلى ذكرى تبقى.</span></h1>
          <p class="up u3">تصوير احترافي للأعراس والبورتريه والمنتجات والفعاليات، بعين فنية وجودة تُرى من أول نظرة.</p>
          <div class="d-flex gap-2 flex-wrap up u4">
            <a href="#work" class="btn btn-warning btn-lg fw-semibold">شاهد أعمالنا</a>
            <a href="#contact" class="btn btn-outline-light btn-lg">تواصل معنا</a>
          </div>
        </div>
        <div class="prints up u3" aria-hidden="true">
          <figure class="p1"><img :src="hero(0)" alt=""></figure>
          <figure class="p2"><img :src="hero(1)" alt=""></figure>
          <figure class="p3"><img :src="hero(2)" alt=""></figure>
        </div>
      </div>

      <div class="container">
        <div class="statbar rv">
          <div v-for="s in stats" :key="s.l" class="stat">
            <b><span :data-count="s.n" v-once>0</span>{{ s.suffix }}</b><small>{{ s.l }}</small>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ SERVICES ============ -->
    <section id="services" class="sec">
      <div class="container">
        <div class="head rv"><span class="eyebrow">ماذا نقدّم</span><h2>خدماتنا</h2><p>كل ما تحتاجه من تصوير في مكان واحد، بمعايير ثابتة وتسليم في الموعد.</p></div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-4" v-for="(s, i) in services" :key="s.t">
            <div class="svc rv" :style="{ transitionDelay: (i % 3) * 100 + 'ms' }">
              <i class="bi" :class="s.icon"></i><h3>{{ s.t }}</h3><p>{{ s.d }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ PORTFOLIO ============ -->
    <section id="work" class="sec dark">
      <div class="container">
        <div class="head rv"><span class="eyebrow">معرض الصور</span><h2>أبرز أعمالنا</h2><p>مختارات من جلساتنا. اضغط على أي صورة لتكبيرها.</p></div>

        <div class="filters rv">
          <button v-for="c in cats" :key="c.k" :class="{ on: cat === c.k }" @click="cat = c.k">{{ c.t }}</button>
        </div>

        <TransitionGroup name="pf" tag="div" class="masonry">
          <figure v-for="(w, i) in filtered" :key="w.src" class="item" @click="open(i)" tabindex="0" @keydown.enter="open(i)">
            <img :src="w.src" :alt="w.title" loading="lazy">
            <figcaption><span>{{ cats.find(c => c.k === w.cat)?.t }}</span><b>{{ w.title }}</b><i class="bi bi-arrows-fullscreen"></i></figcaption>
          </figure>
        </TransitionGroup>
      </div>
    </section>

    <!-- ============ PROCESS ============ -->
    <section id="process" class="sec">
      <div class="container">
        <div class="head rv"><span class="eyebrow">رحلة بسيطة</span><h2>كيف نعمل</h2></div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" v-for="(s, i) in steps" :key="s.n">
            <div class="stp rv" :style="{ transitionDelay: i * 120 + 'ms' }">
              <span>{{ s.n }}</span><h3>{{ s.t }}</h3><p>{{ s.d }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ CONTACT ============ -->
    <section id="contact" class="sec soft">
      <div class="container">
        <div class="head rv"><span class="eyebrow">يسعدنا سماعك</span><h2>تواصل معنا</h2><p>أرسل لنا تفاصيل جلستك وسنعود إليك بأقرب وقت.</p></div>
        <div class="row g-4">
          <div class="col-lg-5">
            <div class="info rv">
              <div class="ic"><i class="bi bi-telephone"></i><div><small>الهاتف</small><a :href="`tel:${site.phone}`" dir="ltr">{{ site.phone }}</a></div></div>
              <div class="ic"><i class="bi bi-envelope"></i><div><small>البريد الإلكتروني</small><a :href="`mailto:${site.email}`">{{ site.email }}</a></div></div>
              <div class="ic"><i class="bi bi-geo-alt"></i><div><small>العنوان</small><span>{{ site.address }}</span></div></div>
              <div class="ic"><i class="bi bi-clock"></i><div><small>ساعات العمل</small><span>{{ site.hours }}</span></div></div>
              <a v-if="site.whatsapp" :href="`https://wa.me/${site.whatsapp}`" target="_blank" rel="noopener" class="btn btn-success w-100 mt-2"><i class="bi bi-whatsapp ms-1"></i> تواصل عبر واتساب</a>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="formc rv">
              <div v-if="sent" class="thanks">
                <i class="bi bi-check-circle-fill"></i>
                <h3>وصلتنا رسالتك</h3>
                <p>شكرًا لتواصلك، سنرد عليك قريبًا على رقم هاتفك.</p>
                <button class="btn btn-outline-dark" @click="sent = false">إرسال رسالة أخرى</button>
              </div>
              <form v-else @submit.prevent="send" novalidate>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">الاسم *</label>
                    <input v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.name }" autocomplete="name">
                    <div class="invalid-feedback">{{ form.errors.name }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">رقم الهاتف *</label>
                    <input v-model="form.phone" class="form-control" :class="{ 'is-invalid': form.errors.phone }" dir="ltr" inputmode="tel" autocomplete="tel">
                    <div class="invalid-feedback">{{ form.errors.phone }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" dir="ltr" autocomplete="email">
                    <div class="invalid-feedback">{{ form.errors.email }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">الخدمة المطلوبة</label>
                    <select v-model="form.service" class="form-select">
                      <option value="">— اختر —</option>
                      <option v-for="s in services" :key="s.t" :value="s.t">{{ s.t }}</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">رسالتك *</label>
                    <textarea v-model="form.message" rows="5" class="form-control" :class="{ 'is-invalid': form.errors.message }"></textarea>
                    <div class="invalid-feedback">{{ form.errors.message }}</div>
                  </div>
                  <!-- فخ للروبوتات: يجب أن يبقى فارغًا -->
                  <input v-model="form.website" tabindex="-1" autocomplete="off" class="hp" aria-hidden="true">
                  <div class="col-12">
                    <button class="btn btn-primary btn-lg w-100" :disabled="form.processing">
                      <span v-if="form.processing" class="spinner-border spinner-border-sm ms-1"></span> إرسال الرسالة
                    </button>
                    <div v-if="form.hasErrors" class="text-danger small mt-2">تحقق من الحقول المعلّمة بالأحمر.</div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer class="foot">
      <div class="container">
        <div class="row g-4 pb-4">
          <div class="col-lg-4">
            <div class="brand mb-3"><img src="/images/station-mark.png" alt="" height="44"><b>{{ site.name }}</b></div>
            <p>{{ site.tagline }} نحوّل لحظاتك إلى صور تبقى. جودة، التزام بالموعد، وذوق فني.</p>
            <div class="soc"><a v-for="s in site.socials" :key="s.label" :href="s.href" :aria-label="s.label" target="_blank" rel="noopener"><i class="bi" :class="s.icon"></i></a></div>
          </div>
          <div class="col-6 col-lg-2"><h4>روابط</h4><a v-for="l in links" :key="l.id" :href="`#${l.id}`">{{ l.t }}</a></div>
          <div class="col-6 col-lg-3"><h4>خدماتنا</h4><a v-for="s in services.slice(0, 5)" :key="s.t" href="#services">{{ s.t }}</a></div>
          <div class="col-lg-3"><h4>تواصل</h4>
            <span><i class="bi bi-telephone"></i> <span dir="ltr">{{ site.phone }}</span></span>
            <span><i class="bi bi-envelope"></i> {{ site.email }}</span>
            <span><i class="bi bi-geo-alt"></i> {{ site.address }}</span>
          </div>
        </div>
        <div class="copy"><span>© {{ new Date().getFullYear() }} {{ site.name }} — {{ site.tagline }}. جميع الحقوق محفوظة.</span><a href="/login">دخول النظام</a></div>
      </div>
    </footer>

    <a href="#top" class="totop" :class="{ show: scrolled }" aria-label="للأعلى"><i class="bi bi-chevron-up"></i></a>

    <!-- ============ LIGHTBOX ============ -->
    <Transition name="fade">
      <div v-if="lb >= 0" class="lb" @click.self="close" role="dialog" aria-modal="true">
        <button class="lb-x" @click="close" aria-label="إغلاق"><i class="bi bi-x-lg"></i></button>
        <button class="lb-n r" @click="prev" aria-label="السابق"><i class="bi bi-chevron-right"></i></button>
        <button class="lb-n l" @click="next" aria-label="التالي"><i class="bi bi-chevron-left"></i></button>
        <figure><img :src="filtered[lb].src" :alt="filtered[lb].title"><figcaption>{{ filtered[lb].title }} · {{ lb + 1 }}/{{ filtered.length }}</figcaption></figure>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.land{--b:#174E77;--d:#0F3553;--dd:#0B2740;--a:#E8A33D;font-family:'IBM Plex Sans Arabic',system-ui,sans-serif;background:#fff;color:#14232F;overflow-x:hidden}
.container{max-width:1140px}
section{scroll-margin-top:70px}

/* header */
.nav{position:fixed;inset:0 0 auto 0;z-index:100;transition:background .3s,box-shadow .3s}
.nav.solid{background:rgba(11,39,64,.94);backdrop-filter:blur(10px);box-shadow:0 4px 24px rgba(0,0,0,.2)}
.nav-in{display:flex;align-items:center;justify-content:space-between;gap:16px;height:72px}
.brand{display:flex;align-items:center;gap:12px;color:#fff;font-size:1.35rem;text-decoration:none}
.links{gap:6px}
.links a{color:#cfe0ee;text-decoration:none;padding:8px 14px;border-radius:8px;transition:.2s;font-weight:500}
.links a:hover,.links a.on{color:#fff;background:rgba(255,255,255,.12)}
.burger{background:transparent;border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:8px;width:42px;height:42px;font-size:1.3rem}
.mobile{display:flex;flex-direction:column;padding:6px 5vw 18px}
.mobile a{color:#fff;text-decoration:none;padding:12px 4px;border-bottom:1px solid rgba(255,255,255,.1)}

/* hero */
.hero{position:relative;background:radial-gradient(1200px 600px at 85% -10%,#2d6a99 0,transparent 60%),linear-gradient(165deg,var(--b),var(--dd));color:#fff;padding:130px 0 0;overflow:hidden}
.hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;padding-bottom:70px}
@media(max-width:991px){.hero-grid{grid-template-columns:1fr;text-align:center}.hero-text .d-flex{justify-content:center}.prints{height:300px!important;margin-top:10px}}
.chip{display:inline-block;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);padding:6px 16px;border-radius:99px;font-size:.92rem;margin-bottom:20px}
.chip i{color:var(--a);margin-inline-end:6px}
h1{font-size:clamp(2.2rem,5.2vw,3.8rem);font-weight:700;line-height:1.28;margin-bottom:18px}
h1 span{color:var(--a)}
.hero p{font-size:1.15rem;color:#c3d7e8;max-width:520px;margin:0 0 28px}
@media(max-width:991px){.hero p{margin-inline:auto}}
.prints{position:relative;height:430px}
.prints figure{position:absolute;margin:0;background:#fff;padding:10px 10px 34px;border-radius:4px;box-shadow:0 24px 50px rgba(0,0,0,.4);transition:transform .5s cubic-bezier(.2,.8,.2,1)}
.prints img{display:block;width:100%;height:100%;object-fit:cover}
.p1{width:230px;height:290px;top:0;right:8%;transform:rotate(-7deg);animation:fl1 7s ease-in-out infinite}
.p2{width:260px;height:200px;top:150px;left:2%;transform:rotate(6deg);animation:fl2 8s ease-in-out infinite}
.p3{width:190px;height:190px;bottom:0;right:24%;transform:rotate(-2deg);animation:fl1 9s ease-in-out infinite reverse}
@keyframes fl1{50%{translate:0 -12px}}@keyframes fl2{50%{translate:0 12px}}
.statbar{display:grid;grid-template-columns:repeat(4,1fr);background:#fff;color:#14232F;border-radius:14px 14px 0 0;box-shadow:0 -10px 40px rgba(0,0,0,.15)}
@media(max-width:767px){.statbar{grid-template-columns:repeat(2,1fr)}}
.stat{padding:26px 12px;text-align:center;border-inline-start:1px solid #e8edf2}
.stat:first-child{border-inline-start:0}
.stat b{display:block;font-size:2rem;color:var(--b);font-weight:700}.stat small{color:#6b7a87}

/* sections */
.sec{padding:96px 0}
.sec.soft{background:#F3F7FA}
.sec.dark{background:var(--dd);color:#fff}
.head{text-align:center;max-width:620px;margin:0 auto 46px}
.eyebrow{display:inline-block;color:var(--a);font-weight:600;letter-spacing:.5px;margin-bottom:6px}
.head h2{font-weight:700;font-size:clamp(1.8rem,4vw,2.5rem)}
.head p{color:#6b7a87;margin:8px 0 0}
.dark .head p{color:#9dbbd3}

.svc{background:#fff;border:1px solid #e3e9ef;border-radius:14px;padding:28px;height:100%;transition:opacity .7s,transform .3s,box-shadow .3s,border-color .3s}
.svc:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(23,78,119,.13);border-color:transparent}
.svc i{display:grid;place-items:center;width:56px;height:56px;border-radius:14px;background:#E8F0F7;color:var(--b);font-size:1.6rem;margin-bottom:16px;transition:.3s}
.svc:hover i{background:var(--b);color:#fff}
.svc h3{font-size:1.15rem;font-weight:600}.svc p{color:#5f6e7b;margin:0}

/* portfolio */
.filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-bottom:34px}
.filters button{background:transparent;border:1px solid rgba(255,255,255,.25);color:#cfe0ee;padding:8px 20px;border-radius:99px;transition:.2s;font-weight:500}
.filters button:hover{border-color:#fff;color:#fff}
.filters button.on{background:var(--a);border-color:var(--a);color:#14232F;font-weight:600}
.masonry{columns:3 260px;column-gap:16px}
.item{position:relative;break-inside:avoid;margin:0 0 16px;border-radius:10px;overflow:hidden;cursor:zoom-in;background:#16405f}
.item img{display:block;width:100%;height:auto;transition:transform .7s cubic-bezier(.2,.8,.2,1)}
.item:hover img,.item:focus-visible img{transform:scale(1.07)}
.item figcaption{position:absolute;inset:auto 0 0 0;padding:44px 16px 14px;background:linear-gradient(transparent,rgba(5,20,34,.85));display:flex;flex-direction:column;opacity:0;transform:translateY(10px);transition:.35s}
.item:hover figcaption,.item:focus-visible figcaption{opacity:1;transform:none}
.item figcaption span{color:var(--a);font-size:.8rem}.item figcaption b{font-weight:600}
.item figcaption i{position:absolute;top:-30px;left:14px;background:rgba(255,255,255,.9);color:var(--d);width:34px;height:34px;border-radius:50%;display:grid;place-items:center}
.pf-enter-active,.pf-leave-active{transition:opacity .35s,transform .35s}
.pf-enter-from,.pf-leave-to{opacity:0;transform:scale(.94)}

/* process */
.stp{position:relative;padding:28px 24px;border-radius:14px;background:#fff;border:1px solid #e3e9ef;height:100%}
.stp span{display:block;font-size:2.4rem;font-weight:700;color:transparent;-webkit-text-stroke:1.5px var(--b);line-height:1;margin-bottom:12px}
.stp h3{font-size:1.1rem;font-weight:600}.stp p{margin:0;color:#5f6e7b}

/* contact */
.info{background:var(--d);color:#fff;border-radius:16px;padding:28px}
.ic{display:flex;gap:14px;align-items:center;padding:14px 0;border-bottom:1px solid rgba(255,255,255,.12)}
.ic:last-of-type{border-bottom:0}
.ic>i{display:grid;place-items:center;width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,.1);color:var(--a);font-size:1.2rem;flex:none}
.ic small{display:block;color:#9dbbd3}.ic a,.ic span{color:#fff;text-decoration:none}
.formc{background:#fff;border:1px solid #e3e9ef;border-radius:16px;padding:30px;box-shadow:0 14px 40px rgba(23,78,119,.08)}
.hp{position:absolute;left:-9999px;opacity:0;height:0;width:0}
.thanks{text-align:center;padding:40px 10px}
.thanks i{font-size:3.2rem;color:#1F7A6B}
.thanks h3{margin:12px 0 6px;font-weight:700}.thanks p{color:#5f6e7b}

/* footer */
.foot{background:#071d30;color:#9dbbd3;padding:70px 0 0}
.foot .brand{font-size:1.3rem}
.foot h4{color:#fff;font-size:1.05rem;margin-bottom:14px}
.foot a,.foot span{display:block;color:#9dbbd3;text-decoration:none;margin-bottom:8px;transition:color .2s}
.foot a:hover{color:var(--a)}
.foot .soc{display:flex;gap:10px;margin-top:14px}
.foot .soc a{display:grid;place-items:center;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.08);color:#fff;margin:0}
.foot .soc a:hover{background:var(--a);color:#14232F}
.copy{display:flex;flex-wrap:wrap;justify-content:space-between;gap:10px;padding:22px 0;border-top:1px solid rgba(255,255,255,.1);font-size:.92rem}
.copy a{display:inline;margin:0}

.totop{position:fixed;bottom:22px;left:22px;z-index:90;width:46px;height:46px;border-radius:50%;background:var(--a);color:#14232F;display:grid;place-items:center;text-decoration:none;box-shadow:0 8px 24px rgba(0,0,0,.3);opacity:0;pointer-events:none;transform:translateY(14px);transition:.3s}
.totop.show{opacity:1;pointer-events:auto;transform:none}

/* lightbox */
.lb{position:fixed;inset:0;z-index:200;background:rgba(4,15,26,.94);display:grid;place-items:center;padding:20px}
.lb figure{margin:0;text-align:center;max-width:min(92vw,1000px)}
.lb img{max-width:100%;max-height:78vh;border-radius:8px;box-shadow:0 30px 80px rgba(0,0,0,.6)}
.lb figcaption{color:#cfe0ee;margin-top:14px}
.lb button{position:absolute;background:rgba(255,255,255,.12);border:0;color:#fff;width:48px;height:48px;border-radius:50%;font-size:1.3rem;transition:.2s}
.lb button:hover{background:var(--a);color:#14232F}
.lb-x{top:20px;left:20px}.lb-n{top:50%;transform:translateY(-50%)}.lb-n.r{right:20px}.lb-n.l{left:20px}
.fade-enter-active,.fade-leave-active{transition:opacity .25s}.fade-enter-from,.fade-leave-to{opacity:0}

/* reveal / intro */
.rv{opacity:0;transform:translateY(26px);transition:opacity .75s ease,transform .75s ease}
.rv.in{opacity:1;transform:none}
.up{opacity:0;transform:translateY(20px);animation:up .85s cubic-bezier(.2,.7,.2,1) forwards}
.u1{animation-delay:.05s}.u2{animation-delay:.2s}.u3{animation-delay:.35s}.u4{animation-delay:.5s}
@keyframes up{to{opacity:1;transform:none}}
@media (prefers-reduced-motion:reduce){
  .up,.rv{opacity:1!important;transform:none!important;animation:none!important;transition:none!important}
  .prints figure{animation:none!important}.item img,.svc{transition:none}
}
</style>
