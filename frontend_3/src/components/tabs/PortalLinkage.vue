<script>

import {useSharedPortalProject} from '../../store/portal.js';
import {useSharedExternalModules} from '../../store/externalModules.js';

export default {
  name: "PortalLinkage",
  data() {
    return {
      portalLinked: false,
      portalLinkageAlertMessage: 'portalLinkageAlertMessage',
      showPortalLinkageDismissibleAlert: 'd-none',
      portalLinkageVariant: 'alert-danger',
      portalLinkageHeader: window.portalLinkageHeader,
      totalFees: useSharedExternalModules().getTotalFees(),
      notifications: window.notifications || {},
      projectPortal: {}
    }
  },
  async mounted() {
    const sharedPortal = useSharedPortalProject();
    this.portalLinked = await sharedPortal.linked(); // Wait for linked to resolve and update portalLinked
    this.projectPortal = await sharedPortal.loadPortalProject();

  },
  methods: {}
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="alert alert-dismissible" :class="[portalLinkageVariant, showPortalLinkageDismissibleAlert]">
          {{ portalLinkageAlertMessage }}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        {{ portalLinkageHeader }}
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h6>
          R2P2 (<u>R</u>esearch IT <u>R</u>esearch <u>P</u>roject <u>P</u>ortal) is a web platform that
          coordinates
          applications, services, and support for researchers working with the Research IT team and Stanford
          Technology and Digital Solutions (TDS).
        </h6>
      </div>
    </div>
    <div v-if="portalLinked === false">
      <div class="card" style="width: 18rem;">
        <div class="card-body">
          <h5 class="card-title">R2P2 - REDCap Linkage</h5>
          <!--          <p class="card-text">-->
          <div v-if="totalFees > 0">
            <div class="row alert alert-danger fade show">
              <div class="col-12 justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                  class="fas fa-exclamation-circle"></i></h5>
                {{ this.notifications.r2p2_tab_rma_card_not_linked_danger_message }}<br>
              </div>
            </div>
          </div>
          <div v-if="totalFees === 0">
            <div class="row alert warning fade show">
              <div class="col-12 justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                  class="fas fa-exclamation-circle"></i></h5>
                {{ this.notifications.r2p2_tab_rma_card_no_fees_warning_message }}<br>
              </div>
            </div>
          </div>
          <div class="text-center">
            <button class="mt-3 success small">
              Find/Create R2P2 Project
            </button>
          </div>
          <!--          </p>-->
        </div>
      </div>
    </div>
    <div v-else>
      <div class="row alert alert-success">
        <div class="col-12 justify-content-center align-self-center"><h5 class="d-inline-block  p-1"><i
            class="fas fa-check-circle"></i></h5>
          {{ this.notifications.r2p2_tab_rma_card_linked_success_message }}
          <a :href="this.projectPortal.data.project_portal_url || ''">
            <span>{{ this.projectPortal.data.project_portal_name }}</span></a>
        </div>
      </div>
      <div class="row">
        <div class="col-12 justify-content-center align-self-center">
          If this is incorrect, please open a support ticket with additional detail for assistance.
        </div>
      </div>
      <div class="row">
        <div class="col-5">
          <button class="mt-3 btn btn-success small">
              Sync REDCap Users to {{ this.projectPortal.data.project_portal_name }} / TODO MODAL
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>