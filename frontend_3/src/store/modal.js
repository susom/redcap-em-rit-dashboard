import { defineStore } from 'pinia';

export const useModalStore = defineStore('modalStore', {
  state: () => ({
    activeModal: null, // Tracks the currently active modal
  }),
  actions: {
    showModal(modalId) {
      this.activeModal = modalId;
      const modalElement = document.getElementById(modalId);
      if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
      }
    },
    hideModal(modalId) {
      const modalElement = document.getElementById(modalId);
      if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
      }
      this.activeModal = null;
    },
  },
});
