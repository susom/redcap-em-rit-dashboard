<script>
import AlertComponent from "./AlertComponent.vue";

export default {
  name: "HeaderComponent",
  components: {AlertComponent},
  data() {
    return {
      showError: false,
      errorMessage: ''
    }
  }
}
</script>

<template>
  <div>
    <div class="row">
      <AlertComponent/>
    </div>
    <h4>
      Welcome to your REDCap R2P2 Dashboard!
    </h4>
    <h6>Use the tabs below to organize your research projects and communicate with Research IT.
    </h6>
    <div class="p-2">
      <!--                <b-alert variant="secondary" show>-->
      <b><i class="fas fa-question-circle"></i> Have Questions? </b> <i><a target="_blank"
                                                                           href="https://medwiki.stanford.edu/x/uiK3Cg">What
      is R2P2?</a> Contact us with a Support Ticket below.</i>
      <!--                </b-alert>-->
    </div>
  </div>
</template>

<style scoped>

</style>