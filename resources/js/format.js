export const money = (n, d = 2) =>
  Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d });

// يقبل الأرقام العربية والفواصل ويحوّلها لرقم إنجليزي صالح (للكتابة في حقول السعر/الكمية)
const AR = '٠١٢٣٤٥٦٧٨٩', PR = '۰۱۲۳۴۵۶۷۸۹';
export const cleanNum = v => {
  const s = String(v ?? '')
    .replace(/[٠-٩]/g, d => AR.indexOf(d))
    .replace(/[۰-۹]/g, d => PR.indexOf(d))
    .replace(/٫/g, '.')
    .replace(/[^0-9.]/g, '');
  const [h, ...t] = s.split('.');
  return t.length ? h + '.' + t.join('') : h;
};

export const roleLabel = r => ({ admin: 'مدير', accountant: 'محاسب', viewer: 'مشاهد' }[r] || r);
