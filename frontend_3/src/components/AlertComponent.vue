<script>
import {useAlertStore} from '../store/useAlertStore.js';
import {computed} from 'vue';

export default {
  name: "AlertComponent",
  setup() {
    const errorStore = useAlertStore();
    const alert = computed(() => errorStore.alert);
    const variant = computed(() => errorStore.variant);
    const dismissible = computed(() => errorStore.dismissible);

    const dismissAlert = () => {
      errorStore.clearAlert();
    };

    return {
      alert,
      variant,
      dismissible,
      dismissAlert,
    };
  }
}
</script>

<template>
  <div>
    <div v-if="alert" class="row float-left">
      <div class="alert" :class="'alert-'+variant">
        <span v-html="alert"></span>
        <button type="button" class="close" v-if="dismissible"  @click="dismissAlert">x</button>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>