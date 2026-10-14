<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { toast } from '../../toast';

const props = defineProps({ items: Array, cats: Object });

const uploadCat = ref('wedding');
const picker = ref(null);
const busy = ref(false);
const prog = ref({ done: 0, total: 0 });
const drag = ref(false);

// تصغير الصورة في المتصفح قبل الرفع (أسرع، ويتجاوز حدود حجم الرفع على السيرفر)
async function shrink(file, max = 1600) {
  try {
    const bmp = await createImageBitmap(file, { imageOrientation: 'from-image' });
    const k = Math.min(1, max / Math.max(bmp.width, bmp.height));
    const c = document.createElement('canvas');
    c.width = Math.round(bmp.width * k); c.height = Math.round(bmp.height * k);
    const ctx = c.getContext('2d');
    ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
    ctx.drawImage(bmp, 0, 0, c.width, c.height);
    return await new Promise(res => c.toBlob(b => res(b || file), 'image/jpeg', 0.85));
  } catch { return file; }
}

async function upload(fileList) {
  const list = [...fileList].filter(f => f.type.startsWith('image/'));
  if (!list.length) return toast.error('اختر ملفات صور (JPG أو PNG أو WebP).');
  busy.value = true; prog.value = { done: 0, total: list.length };
  let ok = 0;
  for (const f of list) {
    try {
      const fd = new FormData();
      fd.append('image', await shrink(f), f.name.replace(/\.\w+$/, '') + '.jpg');
      fd.append('category', uploadCat.value);
      await window.axios.post('/portfolio', fd, { headers: { Accept: 'application/json' } });
      ok++;
    } catch (e) {
      const errs = e.response?.data?.errors;
      toast.error((errs && Object.values(errs)[0][0]) || e.response?.data?.message || `تعذّر رفع ${f.name}`);
    }
    prog.value.done++;
  }
  busy.value = false;
  if (picker.value) picker.value.value = '';
  if (ok) toast.success(`تم رفع ${ok} صورة`);
  router.reload({ only: ['items'] });
}

const onDrop = e => { drag.value = false; upload(e.dataTransfer.files); };

/* تعديل */
const editing = ref(null);
const draft = ref({ title: '', category: '', file: null });
const startEdit = it => { editing.value = it.id; draft.value = { title: it.title, category: it.category, file: null }; };
async function saveEdit(it) {
  const data = { _method: 'put', title: draft.value.title, category: draft.value.category };
  if (draft.value.file) data.image = await shrink(draft.value.file);
  router.post(`/portfolio/${it.id}`, data, { forceFormData: true, preserveScroll: true, onSuccess: () => (editing.value = null) });
}

const toggle = it => router.patch(`/portfolio/${it.id}`, { is_active: !it.is_active }, { preserveScroll: true });
const move = (it, direction) => router.post(`/portfolio/${it.id}/move`, { direction }, { preserveScroll: true });
const del = it => confirm(`حذف "${it.title}" نهائيًا؟`) && router.delete(`/portfolio/${it.id}`, { preserveScroll: true });
</script>

<template>
  <AppLayout title="صور الموقع">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
      <h1 class="page-title">صور الموقع</h1>
      <a href="/?preview=1" target="_blank" class="btn btn-outline-dark"><i class="bi bi-box-arrow-up-right"></i> عرض الموقع</a>
    </div>
    <p class="text-secondary mb-4">هذه الصور تظهر في قسم "أبرز أعمالنا" بالصفحة الرئيسية. أول ثلاث صور تظهر أيضًا في أعلى الصفحة. استخدم الأسهم لترتيبها.</p>

    <!-- رفع -->
    <div class="panel p-3 mb-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">تصنيف الصور المرفوعة</label>
          <select v-model="uploadCat" class="form-select" :disabled="busy">
            <option v-for="(label, key) in cats" :key="key" :value="key">{{ label }}</option>
          </select>
        </div>
        <div class="col-md-8">
          <div class="drop" :class="{ on: drag }" @dragover.prevent="drag = true" @dragleave="drag = false" @drop.prevent="onDrop" @click="!busy && picker.click()">
            <input ref="picker" type="file" accept="image/jpeg,image/png,image/webp" multiple hidden @change="upload($event.target.files)">
            <template v-if="!busy"><i class="bi bi-cloud-arrow-up fs-3"></i><div>اسحب الصور هنا أو اضغط لاختيارها (يمكن اختيار عدة صور)</div></template>
            <template v-else><span class="spinner-border spinner-border-sm"></span> جاري الرفع {{ prog.done }} / {{ prog.total }}</template>
          </div>
        </div>
      </div>
    </div>

    <!-- الصور -->
    <div class="row g-3">
      <div v-for="(it, idx) in items" :key="it.id" class="col-sm-6 col-lg-4 col-xl-3">
        <div class="panel h-100 overflow-hidden" :class="{ dim: !it.is_active }">
          <div class="thumb"><img :src="it.src" :alt="it.title" loading="lazy"><span v-if="!it.is_active" class="hid">مخفية</span></div>

          <div class="p-2">
            <template v-if="editing === it.id">
              <input v-model="draft.title" class="form-control form-control-sm mb-2" placeholder="العنوان">
              <select v-model="draft.category" class="form-select form-select-sm mb-2">
                <option v-for="(label, key) in cats" :key="key" :value="key">{{ label }}</option>
              </select>
              <label class="form-label small mb-1">استبدال الصورة (اختياري)</label>
              <input type="file" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm mb-2" @change="draft.file = $event.target.files[0]">
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary flex-fill" @click="saveEdit(it)">حفظ</button>
                <button class="btn btn-sm btn-outline-secondary" @click="editing = null">إلغاء</button>
              </div>
            </template>
            <template v-else>
              <div class="fw-semibold text-truncate">{{ it.title }}</div>
              <small class="text-secondary">{{ cats[it.category] }}</small>
              <div class="d-flex gap-1 mt-2">
                <button class="btn btn-sm btn-outline-dark" :disabled="idx === 0" @click="move(it, 'up')" title="تقديم"><i class="bi bi-arrow-right"></i></button>
                <button class="btn btn-sm btn-outline-dark" :disabled="idx === items.length - 1" @click="move(it, 'down')" title="تأخير"><i class="bi bi-arrow-left"></i></button>
                <button class="btn btn-sm btn-outline-dark" @click="toggle(it)" :title="it.is_active ? 'إخفاء' : 'إظهار'"><i class="bi" :class="it.is_active ? 'bi-eye' : 'bi-eye-slash'"></i></button>
                <button class="btn btn-sm btn-outline-dark" @click="startEdit(it)" title="تعديل"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger ms-auto" @click="del(it)" title="حذف"><i class="bi bi-trash"></i></button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!items.length" class="panel p-5 text-center text-secondary">
      لا توجد صور بعد، والصفحة الرئيسية تعرض صورًا مؤقتة. ارفع صورك الأولى من الأعلى.
    </div>
  </AppLayout>
</template>

<style scoped>
.drop{border:2px dashed #b9c9d8;border-radius:10px;padding:18px;text-align:center;color:#5f6e7b;cursor:pointer;transition:.2s}
.drop:hover,.drop.on{border-color:var(--ink);background:#eef4f9;color:var(--ink)}
.thumb{position:relative;aspect-ratio:4/3;background:#e8eef4}
.thumb img{width:100%;height:100%;object-fit:cover;display:block}
.hid{position:absolute;top:8px;right:8px;background:#14232F;color:#fff;border-radius:6px;padding:2px 10px;font-size:.8rem}
.dim .thumb img{opacity:.4}
</style>
