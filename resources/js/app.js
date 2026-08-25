// ─── Import Alpine.js ────────────────────────────────────────────
import Alpine from "alpinejs";

// ─── Daftarkan Alpine ke window ─────────────────────────────────
window.Alpine = Alpine;
Alpine.start();

// ─── Toast Notification Helper ──────────────────────────────────
// Dipanggil via: showToast('Berhasil disimpan!', 'success')
window.showToast = function (message, type = "success", duration = 4000) {
  const container =
    document.getElementById("toast-container") || createToastContainer();

  const toast = document.createElement("div");
  const icons = {
    success: `<svg class="w-5 h-5 text-success flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>`,
    error:   `<svg class="w-5 h-5 text-danger flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>`,
    warning: `<svg class="w-5 h-5 text-warning flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>`,
  };

  const typeClass = {
    success: "border-success",
    error:   "border-danger",
    warning: "border-warning",
  };

  toast.className = `flex items-start gap-3 p-4 rounded-2xl shadow-xl bg-white border-l-4
    ${typeClass[type]} min-w-[300px] max-w-sm animate-slide-down mb-3`;

  toast.innerHTML = `
    ${icons[type]}
    <div class="flex-1">
      <p class="text-sm font-semibold text-slate-700">${message}</p>
    </div>
    <button onclick="this.closest('.toast-item').remove()"
      class="text-slate-400 hover:text-slate-600 transition-colors ml-1 flex-shrink-0">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  `;
  toast.classList.add("toast-item");
  container.appendChild(toast);

  // Auto-dismiss setelah durasi
  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateX(100%)";
    toast.style.transition = "all 0.3s ease";
    setTimeout(() => toast.remove(), 300);
  }, duration);
};

function createToastContainer() {
  const container = document.createElement("div");
  container.id = "toast-container";
  container.className = "fixed top-4 right-4 z-50";
  document.body.appendChild(container);
  return container;
}

// ─── Konfirmasi Modal Alpine Component ──────────────────────────
// Digunakan sebagai x-data="confirmModal()" pada elemen
document.addEventListener("alpine:init", () => {
  Alpine.data("confirmModal", (options = {}) => ({
    show: false,
    title: options.title || "Konfirmasi",
    message: options.message || "Apakah Anda yakin?",
    formId: options.formId || null,

    open(title, message, formId) {
      this.title = title;
      this.message = message;
      this.formId = formId;
      this.show = true;
      document.body.style.overflow = "hidden";
    },

    close() {
      this.show = false;
      document.body.style.overflow = "";
    },

    confirm() {
      if (this.formId) {
        document.getElementById(this.formId)?.submit();
      }
      this.close();
    },
  }));

  // ── Toggle sidebar mobile ──
  Alpine.data("sidebar", () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
  }));

  // ── Dropdown menu ──
  Alpine.data("dropdown", () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; },
  }));
});

// ─── Auto-dismiss flash messages dari server ─────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const flashMessages = document.querySelectorAll("[data-flash-auto-dismiss]");
  flashMessages.forEach((el) => {
    const duration = parseInt(el.dataset.flashAutoDismiss) || 4000;
    setTimeout(() => {
      el.style.opacity = "0";
      el.style.transform = "translateX(100%)";
      el.style.transition = "all 0.3s ease";
      setTimeout(() => el.remove(), 300);
    }, duration);
  });
});
