import { reactive } from 'vue';

export const toasts = reactive([]);
let id = 0;

function push(type, msg) {
  const t = { id: ++id, type, msg };
  toasts.push(t);
  setTimeout(() => {
    const i = toasts.findIndex(x => x.id === t.id);
    if (i > -1) toasts.splice(i, 1);
  }, 5000);
}

export const toast = {
  success: m => push('success', m),
  error: m => push('danger', m),
};
