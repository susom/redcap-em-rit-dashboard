<template>
  <div v-if="isVisible" class="overlay">
    <div class="loader">Loading...</div>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useOverlayStore } from '../store/overlayStore';

export default {
  setup() {
    const overlayStore = useOverlayStore();
    const isVisible = computed(() => overlayStore.isVisible);

    return {
      isVisible,
    };
  },
};
</script>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* semi-transparent background */
  z-index: 1000; /* ensure it's above everything else */
}

.loader {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white; /* loader text color */
  font-size: 24px; /* adjust as needed */
}
</style>
Step