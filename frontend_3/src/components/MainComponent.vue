<script>

import PortalLinkage from "../components/tabs/PortalLinkage.vue";
import RMAComponent from "../components/tabs/RMAComponent.vue";
import SupportTickets from "../components/tabs/SupportTickets.vue";
import ExternalModules from "../components/tabs/ExternalModules.vue";
import ProfessionalServices from "../components/tabs/ProfessionalServices.vue";
import {useSharedPortalProject} from '../store/portal.js';

export default {
  name: "MainComponent",
  components: {ProfessionalServices, ExternalModules, SupportTickets, RMAComponent, PortalLinkage},
  data () {
    return {
      redcapStep: 0,
      rmaTabVariant: 'text-danger',
      rmaTabIcon: 'fa-exclamation-circle'
    }
  },
  async mounted() {
    const sharedPortal = useSharedPortalProject();
    this.redcapStep = await sharedPortal.determineREDCapStep();
    console.log(this.redcapStep)
    if (this.redcapStep === 4) {
      this.rmaTabVariant = 'text-success';
      this.rmaTabIcon = 'fa-check-circle';
    }
  },
}
</script>

<template>
  <div class="container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="portal-linkage-tab" data-bs-toggle="tab" data-bs-target="#portal-linkage"
                type="button"
                role="tab" aria-controls="home" aria-selected="true">Portal Linkage
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="rma-tab" data-bs-toggle="tab" data-bs-target="#rma" type="button"
                role="tab" aria-controls="rma" aria-selected="false">REDCap Maintenance Agreement
            <h5 class="d-inline-block  p-1"><i
                  class="fas" :class="[this.rmaTabIcon, this.rmaTabVariant]"></i></h5>
        </button>


      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="support-tickets-tab" data-bs-toggle="tab" data-bs-target="#support-tickets"
                type="button"
                role="tab" aria-controls="support-tickets" aria-selected="false">Support Tickets
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="external-modules-tab" data-bs-toggle="tab" data-bs-target="#external-modules"
                type="button"
                role="tab" aria-controls="external-modules" aria-selected="false">External Modules (EM)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="professional-services-tab" data-bs-toggle="tab"
                data-bs-target="#professional-services"
                type="button"
                role="tab" aria-controls="professional-services" aria-selected="false">Professional Services
        </button>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="portal-linkage" role="tabpanel" aria-labelledby="portal-linkage-tab">
        <PortalLinkage/>
      </div>
      <div class="tab-pane fade" id="rma" role="tabpanel" aria-labelledby="rma-tab">
        <RMAComponent/>
      </div>
      <div class="tab-pane fade" id="support-tickets" role="tabpanel" aria-labelledby="support-tickets-tab">
        <SupportTickets/>
      </div>
      <div class="tab-pane fade" id="external-modules" role="tabpanel" aria-labelledby="external-modules-tab">
        <ExternalModules/>
      </div>
      <div class="tab-pane fade" id="professional-services" role="tabpanel" aria-labelledby="professional-services-tab">
        <ProfessionalServices/>
      </div>
    </div>
  </div>
</template>

<style scoped>
.tab-content {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  border-top: 1px solid #ddd;
  padding: 10px;
}

.nav-tabs {
  margin-bottom: 0;
}
</style>