<template>
  <div>
    <button @click="loadData">Load Data</button>
  </div>
</template>

<script>
import { useOverlayStore } from '../store/overlayStore';

export default {
  setup() {
    const overlayStore = useOverlayStore();

    const loadData = async () => {
      overlayStore.showOverlay(); // Show the overlay
      try {
        // Simulating an async operation
        await new Promise(resolve => setTimeout(resolve, 2000)); // Simulate a 2-second delay
      } catch (error) {
        console.error(error);
      } finally {
        overlayStore.hideOverlay(); // Hide the overlay after operation
      }
    };

    return {
      loadData,
    };
  },
};
</script>