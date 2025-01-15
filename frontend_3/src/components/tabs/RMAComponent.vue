<script>
import {useSharedPortalProject} from '../../store/portal.js';

export default {
  name: "RMAComponent",
  data() {
    return {
      notifications: window.notifications || {},
      ajax_urls: window.ajax_urls || {},
      redcapStep: 0,

    }
  },
  methods: {
    async generateRMA() {
      console.log('Generating RMA')
    },
    async appendToExistingRMA() {
      console.log('Appending to existing RMA')
    },
    isUserHasPermission(roles) {
      console.log('TODO')
      return false;
    }
  },
  async mounted() {
    const sharedPortal = useSharedPortalProject();
    this.redcapStep = await sharedPortal.determineREDCapStep();

  },
}
</script>

<template>
  <div class="container-fluid">
    <div v-if="this.redcapStep < 4" class="row">
      <div class="col-12">
        <p>
          We recommend all REDCap projects be bound to an approved REDCap Maintenance Agreement. Some
          projects (e.g. those with external modules that have fees or other custom code) will require an
          approved REDCap Maintenance Agreement prior to being moved to Production mode, so it is best to
          complete these steps before going live to avoid delays.
        </p>
        <p>
          <i>Additional details on the maintenance agreement can be found
            at the <a href="https://medwiki.stanford.edu/x/dZeWCg" target="_blank" class="ml-1"><i
                class="fas fa-external-link-alt"></i> REDCap Wiki </a>
          </i>
        </p>
        <div class="row alert alert-danger d-flex d-inline-block">
          <div class="col-12 justify-content-center align-self-center" lg="12"><h5
              class="d-inline-block  p-1"><i
              class="fas fa-exclamation-circle"></i></h5>
            {{ notifications.r2p2_tab_rma_card_danger_message }}
          </div>
        </div>
      </div>
    </div>
    <div v-if="this.redcapStep === 1">
      <div class="row">
        <div class="col-6">
          <button class="btn btn-success" @click="this.generateRMA()">
            Step 1: Generate a REDCap Maintenance Agreement
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <p>
            After the REDCap Maintenance agreement has been created, a R2P2 project admin or finance
            user will need to approve the agreement and provide a PTA on the R2P2 portal.
          </p>
        </div>
      </div>
    </div>
    <div v-else-if="this.redcapStep > 1">
      <div class="row">
        <div class="col-6">
          <div class="text-success justify-content-start align-self-center">
            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
            Step 1: REDCap Maintenance Agreement Created
          </div>
        </div>
      </div>
    </div>
    <div v-if="this.redcapStep === 2">
      <div class="row">
        <div class="col-6">
          <button class="btn btn-success" @click="this.appendToExistingRMA()">
            Step 2: Add this REDCap Project to the R2P2 REDCap Maintenance Agreement
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <p>
            The linked R2P2 project already has a maintenance agreement in place but this particular
            REDCap
            project is not associated to it. Click below to add this REDCap project to the existing
            maintenance agreement.
          </p>
        </div>
      </div>
    </div>
    <div v-else-if="this.redcapStep > 2">
      <div class="row">
        <div class="col-6">
          <div class="text-success justify-content-start align-self-center">
            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
            Step 2: REDCap project linked to R2P2 REDCap Maintenance Agreement
          </div>
        </div>
      </div>
    </div>
    <div v-if="this.redcapStep === 3">
      <div class="row">
        <div class="col-6">
          <div class="text-danger justify-content-start align-self-center">
            <h5 class="d-inline-block  p-1"><i class="far fa-circle"></i></h5>
            Step 3: The linked R2P2 REDCap Maintenance Agreement is awaiting approval.
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <div class="row">
            <div class="col-6">
              <button :disabled="!this.isUserHasPermission([2,3])" class="pl-4">
                Approve RMA / TODO MODAL
              </button>
            </div>
          </div>
          <div>
            <i>
              Approval requires R2P2 admin or PI role
            </i>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="this.redcapStep > 3">
      <div class="row">
        <div class="col-6">
          <div class="text-success justify-content-start align-self-center">
            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
            Step 3: REDCap Maintenance Agreement Approved.
          </div>
        </div>
      </div>
    </div>

    <div v-if="this.redcapStep === 4">
      <div class="row">
        <div class="col-6">
          <p>
            Additional details on the REDCap Maintenance Agreement can be found
            at the <a href="https://medwiki.stanford.edu/x/dZeWCg" target="_blank" class="ml-1"><i
              class="fas fa-external-link-alt"></i> REDCap Wiki </a>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-6 d-flex d-inline-block">
          <div class="alert alert-success">
            <h5
                class="d-inline-block  p-1"><i
                class="fas fa-check-circle"></i></h5> This REDCap Project is linked to
            an
            approved
            REDCap Maintenance Agreement.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>