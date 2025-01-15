// stores/errorStore.js
import { defineStore } from 'pinia';

export const useAlertStore = defineStore('alerts', {
  state: () => ({
    alert: null, // Holds the current error message or object
    variant: 'danger', // The variant of the alert
    dismissible: false, // Whether the alert can be dismissed
    display : false,
  }),
  actions: {
    setAlert(alert, variant, dismissible) {
      this.alert = alert;
      this.variant = variant;
      this.dismissible = dismissible;
      this.display = true
    },
    clearAlert() {
      this.alert = null;
      this.variant = null;
      this.dismissible = null;
      this.display = false
    },
  },
});
