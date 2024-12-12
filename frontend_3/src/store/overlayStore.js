// store/overlayStore.js
import { defineStore } from 'pinia';

export const useOverlayStore = defineStore('overlay', {
  state: () => ({
    isVisible: false,
  }),
  actions: {
    showOverlay() {
      this.isVisible = true;
    },
    hideOverlay() {
      this.isVisible = false;
    },
  },
});