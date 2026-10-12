<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onBeforeUnmount, ref } from 'vue';

const demo = ref([
  { label: 'واردات الشهر', target: 18450, value: 0, cls: 'in' },
  { label: 'صادرات الشهر', target: 6200, value: 0, cls: 'ex' },
  { label: 'صافي الربح', target: 12250, value: 0, cls: 'pf' },
]);

const features = [
  { icon: 'bi-arrow-repeat', t: 'فواتير تتكرر وحدها', d: 'الإيجار، اشتراك زبون ثابت، أجر مصور شهري: حدّدها مرة واحدة ويُنشئها النظام كل شهر في يومها.' },
  { icon: 'bi-envelope-paper', t: 'تقرير شهري إلى بريدك', d: 'في أول كل شهر يصلك ملخص الواردات والصادرات والأرباح وحصة كل شريك دون أن تفتح الحاسوب.' },
  { icon: 'bi-pie-chart', t: 'تقسيم الأرباح تلقائيًا', d: 'حصص الشركاء 30% / 30% / 40% تُحسب لحظيًا من الأرقام الفعلية، لا حاجة لآلة حاسبة.' },
  { icon: 'bi-shield-check', t: 'صلاحيات واضحة', d: 'مدير ومحاسب ومشاهد. كل شخص يرى ويفعل ما يخصه فقط، وأي خطأ يُعرض برسالة مفهومة.' },
];
const steps = [
  { n: '1', t: 'سجّل زبائنك ومصوريك' },
  { n: '2', t: 'أنشئ الفواتير أو اتركها تلقائية' },
  { n: '3', t: 'استلم التقرير والأرباح جاهزة' },
];

let io, raf;
const animate = () => {
  const t0 = performance.now(), dur = 1600;
  const tick = now => {
    const k = Math.min((now - t0) / dur, 1), e = 1 - Math.pow(1 - k, 3);
    demo.value.forEach(d => (d.value = Math.round(d.target * e)));
    if (k < 1) raf = requestAnimationFrame(tick);
  };
  raf = requestAnimationFrame(tick);
};

onMounted(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) { demo.value.forEach(d => (d.value = d.target)); document.querySelectorAll('.rv').forEach(e => e.classList.add('in')); return; }
  animate();
  io = new IntersectionObserver(es => es.forEach(e => e.isIntersecting && (e.target.classList.add('in'), io.unobserve(e.target))), { threshold: .15 });
  document.querySelectorAll('.rv').forEach(e => io.observe(e));
});
onBeforeUnmount(() => { io && io.disconnect(); cancelAnimationFrame(raf); });
const fmt = n => n.toLocaleString('en-US');
</script>

<template>
  <Head title="Station — حسابات مكتب التصوير" />
  <div class="land">
    <header class="nav">
      <div class="brand"><img src="/images/station-mark.png" alt="" height="40"><b>Station</b></div>
      <div class="d-flex gap-2">
        <Link href="/login" class="btn btn-outline-light">دخول</Link>
        <Link href="/register" class="btn btn-warning fw-semibold">ابدأ الآن</Link>
      </div>
    </header>

    <section class="hero">
      <div class="hero-in">
        <h1 class="up u1">حسابات مكتب التصوير<br><span>تعمل وحدها.</span></h1>
        <p class="up u2">فواتير الواردات والصادرات، أجور المصورين، تقارير الشهر وتقسيم الأرباح… كلها مرتبة ومؤتمتة في مكان واحد.</p>
        <div class="up u3 d-flex gap-2 flex-wrap justify-content-center">
          <Link href="/register" class="btn btn-warning btn-lg fw-semibold">إنشاء حساب مجاني</Link>
          <Link href="/login" class="btn btn-outline-light btn-lg">تسجيل الدخول</Link>
        </div>
      </div>

      <div class="strip up u4">
        <div class="row g-3">
          <div class="col-md-4" v-for="d in demo" :key="d.label">
            <div class="frame" :class="{ profit: d.cls === 'pf' }">
              <div class="lbl">{{ d.label }}</div>
              <div class="val" :class="'v-' + d.cls">{{ fmt(d.value) }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center small up u4" style="color:#9DBBD3">* أرقام توضيحية</div>
    </section>

    <section class="sec">
      <h2 class="rv">لماذا Station؟ لأنه يشتغل بدلًا عنك</h2>
      <div class="row g-4 mt-1">
        <div class="col-md-6" v-for="(f, i) in features" :key="f.t">
          <div class="card-f rv" :style="{ transitionDelay: i * 90 + 'ms' }">
            <i class="bi" :class="f.icon"></i>
            <div><h3>{{ f.t }}</h3><p>{{ f.d }}</p></div>
          </div>
        </div>
      </div>
    </section>

    <section class="sec steps">
      <h2 class="rv">ثلاث خطوات فقط</h2>
      <div class="row g-4 mt-1">
        <div class="col-md-4" v-for="(s, i) in steps" :key="s.n">
          <div class="step rv" :style="{ transitionDelay: i * 120 + 'ms' }"><span>{{ s.n }}</span>{{ s.t }}</div>
        </div>
      </div>
    </section>

    <section class="cta rv">
      <img src="/images/station-mark.png" alt="" height="64">
      <h2>جاهز لترتيب حسابات مكتبك؟</h2>
      <Link href="/register" class="btn btn-warning btn-lg fw-semibold">ابدأ الآن</Link>
    </section>

    <footer>© Station — مكتب تصوير</footer>
  </div>
</template>

<style scoped>
.land{--b:#174E77;--d:#0F3553;font-family:'IBM Plex Sans Arabic',system-ui,sans-serif;background:#fff;color:#14232F;overflow-x:hidden}
.nav{position:absolute;inset:0 0 auto 0;z-index:5;display:flex;justify-content:space-between;align-items:center;padding:18px 5vw}
.brand{display:flex;align-items:center;gap:12px;color:#fff;font-size:1.4rem}
.hero{background:linear-gradient(180deg,var(--b),var(--d));color:#fff;padding:130px 5vw 70px}
.hero-in{max-width:760px;margin:0 auto;text-align:center}
h1{font-size:clamp(2.2rem,6vw,4rem);font-weight:700;line-height:1.25}
h1 span{color:#E8A33D}
.hero p{font-size:1.2rem;color:#BCD3E5;margin:18px 0 28px}
.strip{max-width:900px;margin:56px auto 10px}
.strip .frame{background:var(--b)}
.sec{max-width:1000px;margin:0 auto;padding:80px 5vw 0}
.sec h2{text-align:center;font-weight:700;font-size:clamp(1.5rem,3.5vw,2.2rem)}
.card-f{display:flex;gap:16px;background:#fff;border:1px solid #DDE4EB;border-radius:10px;padding:22px;height:100%;transition:transform .25s,box-shadow .25s}
.card-f:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(23,78,119,.12)}
.card-f i{font-size:1.8rem;color:var(--b);background:#E8F0F7;border-radius:10px;width:54px;height:54px;flex:none;display:grid;place-items:center}
.card-f h3{font-size:1.1rem;font-weight:600}.card-f p{margin:0;color:#5b6b78}
.step{display:flex;align-items:center;gap:14px;background:#F1F6FA;border-radius:10px;padding:20px;font-weight:500}
.step span{background:var(--b);color:#fff;border-radius:50%;width:40px;height:40px;flex:none;display:grid;place-items:center;font-weight:700}
.cta{margin:90px 5vw 0;background:var(--b);color:#fff;border-radius:16px;text-align:center;padding:56px 20px}
.cta h2{font-weight:700;margin:16px 0 24px}
footer{text-align:center;color:#7b8a96;padding:40px 0}

/* animation */
.up{opacity:0;transform:translateY(18px);animation:up .8s cubic-bezier(.2,.7,.2,1) forwards}
.u1{animation-delay:.05s}.u2{animation-delay:.2s}.u3{animation-delay:.35s}.u4{animation-delay:.5s}
@keyframes up{to{opacity:1;transform:none}}
.rv{opacity:0;transform:translateY(24px);transition:opacity .7s ease,transform .7s ease}
.rv.in{opacity:1;transform:none}
.strip::before,.strip::after{animation:film 6s linear infinite}
@keyframes film{to{background-position:-20px 0}}
@media (prefers-reduced-motion:reduce){.up,.rv{opacity:1;transform:none;animation:none;transition:none}.strip::before,.strip::after{animation:none}}
</style>
