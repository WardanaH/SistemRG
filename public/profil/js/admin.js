document.addEventListener('DOMContentLoaded', () => {
  // ======================================================
  // Repeatable Hero Name (max 6)
  // ======================================================
  const list = document.getElementById('heroNameList');
  const addBtn = document.getElementById('btnAddHeroName');

  function updateAddButton() {
    if (!list || !addBtn) return;
    const max = parseInt(list.dataset.max || "6", 10);
    const rows = list.querySelectorAll('.repeat-row').length;
    addBtn.disabled = rows >= max;
  }

  function makeRow(value = "") {
    const row = document.createElement('div');
    row.className = 'repeat-row';
    row.innerHTML = `
      <input type="text" class="form-control" value="${value}">
      <div class="repeat-actions">
        <button type="button" class="btn btn-light border btn-sm" data-move="up" title="Naik"><i class="bi bi-arrow-up"></i></button>
        <button type="button" class="btn btn-light border btn-sm" data-move="down" title="Turun"><i class="bi bi-arrow-down"></i></button>
        <button type="button" class="btn btn-light border btn-sm" data-remove title="Hapus"><i class="bi bi-x-lg"></i></button>
      </div>
    `;
    return row;
  }

  if (list) {
    list.addEventListener('click', (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;
      const row = btn.closest('.repeat-row');
      if (!row) return;

      if (btn.hasAttribute('data-remove')) {
        row.remove();
        updateAddButton();
        return;
      }

      const dir = btn.getAttribute('data-move');
      if (dir === 'up') {
        const prev = row.previousElementSibling;
        if (prev) row.parentNode.insertBefore(row, prev);
      }
      if (dir === 'down') {
        const next = row.nextElementSibling;
        if (next) row.parentNode.insertBefore(next, row);
      }
    });
  }

  if (addBtn && list) {
    addBtn.addEventListener('click', () => {
      const max = parseInt(list.dataset.max || "6", 10);
      const rows = list.querySelectorAll('.repeat-row').length;
      if (rows >= max) return;

      list.appendChild(makeRow(""));
      updateAddButton();
    });
  }

  updateAddButton();

  // ======================================================
  // Hero Image Preview
  // ======================================================
  const heroInput = document.getElementById('heroImageInput');
  const heroPrev = document.getElementById('heroImagePreview');

  if (heroInput && heroPrev) {
    heroInput.addEventListener('change', () => {
      const file = heroInput.files && heroInput.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      heroPrev.innerHTML = `<img src="${url}" alt="Preview Hero">`;
    });
  }

  // ======================================================
  // Color mode: enable hex only when custom
  // ======================================================
  document.querySelectorAll('.color-mode').forEach(sel => {
    const targetName = sel.getAttribute('data-target');
    const picker = document.querySelector(`.color-picker[data-name="${targetName}"]`);
    const hex = document.querySelector(`.color-hex[data-name="${targetName}"]`);

    function syncState() {
      const v = sel.value;
      const isCustom = v === 'custom';
      if (picker) picker.disabled = !isCustom;
      if (hex) hex.disabled = !isCustom;

      if (!isCustom && v) {
        if (hex) hex.value = v;
      }
    }

    sel.addEventListener('change', syncState);
    syncState();
  });

  document.querySelectorAll('.color-picker').forEach(picker => {
    picker.addEventListener('input', () => {
      const name = picker.getAttribute('data-name');
      const hex = document.querySelector(`.color-hex[data-name="${name}"]`);
      if (hex) hex.value = picker.value;
    });
  });

  // ======================================================
  // Back to Top (GLOBAL)
  // ======================================================
  const backTop = document.getElementById('rgBackTop');

  function toggleBackTop() {
    if (!backTop) return;
    const y = window.scrollY || document.documentElement.scrollTop;
    if (y > 240) backTop.classList.add('is-show');
    else backTop.classList.remove('is-show');
  }

  if (backTop) {
    window.addEventListener('scroll', toggleBackTop, { passive: true });
    toggleBackTop();
    backTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ======================================================
  // Modal Confirm Logout (GLOBAL)
  // ======================================================
  const btnLogout = document.getElementById('btnLogout');
  const logoutModalEl = document.getElementById('rgLogoutModal');

  if (btnLogout && logoutModalEl && window.bootstrap) {
    const logoutModal = new bootstrap.Modal(logoutModalEl);
    btnLogout.addEventListener('click', (e) => {
      e.preventDefault();
      logoutModal.show();
    });
  }

  // ======================================================
  // Modal Confirm Save (GLOBAL)
  // - intercept submit button click (semua halaman edit)
  // - skip confirm if form has data-skip-confirm="1"
  // - ✅ FIX: jangan intercept form DELETE
  // ======================================================
  const saveModalEl = document.getElementById('rgSaveModal');
  const saveConfirmBtn = document.getElementById('rgSaveConfirmBtn');

  let pendingForm = null;

  function getFormSpoofedMethod(form) {
    if (!form) return "POST";
    const spoof = form.querySelector('input[name="_method"]');
    if (spoof && spoof.value) return String(spoof.value).toUpperCase();
    const m = form.getAttribute('method') || 'POST';
    return String(m).toUpperCase();
  }

  if (saveModalEl && saveConfirmBtn && window.bootstrap) {
    const saveModal = new bootstrap.Modal(saveModalEl);

    document.addEventListener('click', (e) => {
      const submitBtn = e.target.closest('button[type="submit"], input[type="submit"]');
      if (!submitBtn) return;

      const form = submitBtn.closest('form');
      if (!form) return;

      // ✅ jangan intercept DELETE
      const method = getFormSpoofedMethod(form);
      if (method === 'DELETE') return;

      // skip confirm if specified
      if (form.hasAttribute('data-skip-confirm') || form.dataset.skipConfirm === "1") return;

      // kalau tombol submitnya disable, biarin
      if (submitBtn.disabled) return;

      // hentikan submit langsung
      e.preventDefault();

      pendingForm = form;
      saveModal.show();
    });

    saveConfirmBtn.addEventListener('click', () => {
      if (!pendingForm) return;
      const formToSubmit = pendingForm;
      pendingForm = null;
      formToSubmit.submit();
    });

    saveModalEl.addEventListener('hidden.bs.modal', () => {
      pendingForm = null;
    });
  }
});
