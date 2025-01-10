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
      portalLinkageHeader: window.portalLinkageHeader + 'TEST REMOVE',
      totalFees: useSharedExternalModules().getTotalFees(),
      notifications: window.notifications || {},
    }
  },
  async mounted() {
    const sharedPortal = useSharedPortalProject();
    this.portalLinked = await sharedPortal.linked(); // Wait for linked to resolve and update portalLinked
    console.log(this.portalLinked)
  },
  methods: {
    linked: function () {
      if (this.ticket.project_portal_id !== '' && this.ticket.project_portal_id_saved === "true") {
        return true;
      }
      return false;
    },
  }
}
</script>

<template>
  <div>
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
  </div>
</template>

<style scoped>

</style>