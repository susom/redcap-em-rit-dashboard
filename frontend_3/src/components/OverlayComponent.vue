<template>
  <div v-if="isOverlayVisible" class="overlay">
    <div class="spinner">Loading...</div>
  </div>
</template>

<script>
import { useOverlayStore } from '../store/overlayStore.js';
import { computed } from 'vue';

export default {
  setup() {
    const overlayStore = useOverlayStore();
    const isOverlayVisible = computed(() => overlayStore.isOverlayVisible);

    return { isOverlayVisible };
  },
};
</script>

<style>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.spinner {
  color: white;
  font-size: 1.5rem;
}
</style>
