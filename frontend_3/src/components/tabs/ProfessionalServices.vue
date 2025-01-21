<script>
import {useSharedPortalProject} from '../../store/portal.js';
import {useModalStore} from '../../store/modal.js';
import {useAlertStore} from '../../store/useAlertStore.js';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import axios from 'axios';

DataTable.use(DataTablesCore);
DataTablesCore.Responsive.bootstrap(bootstrap);
export default {
  name: "ProfessionalServices",
  components: {
    DataTable
  },
  setup() {
    const modalStore = useModalStore();

    const showModal = (modalId) => {
      modalStore.showModal(modalId);
    };

    return {showModal};
  },
  data() {
    return {
      useAlertStore: useAlertStore(),
      notifications: window.notifications || {},
      loading: false,
      ajax_urls: window.ajax_urls || {},
      redcapStep: 0,
      sharedPortalStore: useSharedPortalProject(),
      display_historical_sprint_blocks: false,
      historical_sprint_blocks: [],
      empty_sprint_block: 'No Support Blocks Found',
      professional_services_fields: [
        {
          data: 'id',
          sortable: true
        },
        {
          data: 'title',
          sortable: true
        },
        {
          data: 'status',
          sortable: true
        },
        {
          data: 'amount',
          sortable: true
        },
        {
          data: 'created_at',
          sortable: true
        },
        {
          data: 'reviewed_by',
          sortable: true
        }
      ],
    }
  },
  methods: {
    async getSprintBlocks(show) {
      this.loading = true
      axios.get(this.ajax_urls.get_sprint_blocks).then(response => {
        this.historical_sprint_blocks = response.data.sprint_blocks
        if (this.historical_sprint_blocks == undefined || this.historical_sprint_blocks.length == 0) {
          this.empty_sprint_block = 'No Support Blocks Found'
        }
        this.display_historical_sprint_blocks = show
      }).catch(err => {
        this.useAlertStore.setAlert(err.response.data.message, 'danger', true)
      }).finally(() => {
        this.loading = false
      });
    }
  },
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        Research IT provides professional services to the Stanford community with cost recovery for hands-on
        assistance, software development, and more.
      </div>
    </div>
    <div v-if="this.sharedPortalStore.linked() === false" class="row">
      <div class="col-12">
        In order to request a support block, please first link this REDCap project to a R2P2 Research Project.
        See the first tab for more details.
      </div>
    </div>
    <div v-else class="row">
      <div class="row">
        <div class="col-12">
          To easily request professional assistance, a “Support Block” Statement of Work can be created from this
          page. The size of the block and dollar amount should come from documentation or a conversation with RIT
          personnel. All Support Blocks represent an estimate of time and work and do not guarantee project
          completion. Additional Support Blocks may be required.
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          After a Support Block is requested, a new Statement of Work (SoW) will be automatically created in R2P2.
          Before work can being, you or someone from the research team must approve the SoW and provide a PTA.
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          To learn more about R2P2 and Professional Services view the <a target="_blank"
                                                                         href="https://medwiki.stanford.edu/x/uiK3Cg"><i
            class="fas fa-external-link-alt"></i> The R2P2 wiki</a>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <button class="btn btn-success btn-sm" @click="showModal('professional-services-modal')">Generate Support
            Block
          </button>
        </div>
      </div>
      <div class="row float-left">
        <div class="col-12">
          <button @click="getSprintBlocks(true);" class="btn btn-secondary btn-sm mt-3 "
                  v-if="display_historical_sprint_blocks === false">Show Historical Support Blocks
          </button>
          <button @click="display_historical_sprint_blocks = false" class="btn btn-secondary btn-sm mt-3"
                  v-if="display_historical_sprint_blocks === true">Hide Historical Support Blocks
          </button>
        </div>
      </div>
      <div v-if="display_historical_sprint_blocks === true">
        <div class="row">
          <div class="col-12">
            <a :href="ticket.project_portal_sow_url" target="_blank" class="ml-1"><i
                class="fas fa-external-link-alt"></i>
              <span>Full list of Project SOWs</span></a>
          </div>
        </div>
        <div class="row">
          <div class="row">

            <div class="col-12">
              <DataTable
                  :columns="this.professional_services_fields"
                  :data="this.historical_sprint_blocks"
                  :options="{
            responsive: true,
            language: {
              loadingRecords: 'Loading Sprint blocks, please wait...', // Custom loading message
              emptyTable: loading ? 'Loading Sprint blocks...' : this.empty_sprint_block,
                },
              }"
                  class="table table-hover table-striped nowrap"
              >
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Reviewed by</th>
                </tr>
                </thead>
              </DataTable>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<style scoped>

</style>