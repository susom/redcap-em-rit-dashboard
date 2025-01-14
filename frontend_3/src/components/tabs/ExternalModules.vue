<script>

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import {useSharedPortalProject} from '../../store/portal.js';
import {useSharedExternalModules} from '../../store/externalModules.js';

DataTable.use(DataTablesCore);
DataTablesCore.Responsive.bootstrap(bootstrap);
export default {
  name: "ExternalModules",
  components: {
    DataTable
  },
  data() {
    return {
      sharedPortalStore: useSharedPortalProject(),
      sharedExternalModulesStore: useSharedExternalModules(),
      external_modules_header: window.external_modules_header || '',
      ajax_urls: window.ajax_urls || {},
      notifications: window.notifications,
      external_modules: [],
      all_external_modules: [],
      all_support_tickets: [],
      loading: false,
      empty_tickets_table_message: '',
      EMAlertMessage: '',
      showEMDismissibleAlert: false,
      EMVariant: '',
      external_modules_length: 0,
      monthly_total: 0,
      redcapStep: 0,
      external_modules_fields: [
        {
          data: 'prefix',
          label: 'Name',
          sortable: true
        },
        {
          data: 'maintenance_monthly_cost',
          label: 'Monthly Maintenance Cost',
          sortable: true
        }
      ],
    };
  },
  async mounted() {
    this.external_modules = await this.sharedExternalModulesStore.loadExternalModules();
    this.monthly_total = await this.sharedExternalModulesStore.totalFees;
    console.log(this.monthly_total)
    this.redcapStep = this.sharedPortalStore.determineREDCapStep();
  },
  methods: {
    updateExternalModuleList: function () {
      console.log('Update External Module List');
    }
  }
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="alert alert-dismissible fade show" :class="alert-EMVariant" role="alert"
             v-if="showEMDismissibleAlert">
          <span v-html="EMAlertMessage"></span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <span v-html="external_modules_header"></span>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <span v-html="notifications.update_em_list"></span>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <button @click="updateExternalModuleList()">
          Update External Modules List
        </button>
      </div>
    </div>
    <div class="row">

      <div class="col-12">
        <DataTable
            :columns="this.external_modules_fields"
            :data="this.external_modules"
            :options="{
            responsive: true,
            language: {
              loadingRecords: 'Loading tickets, please wait...', // Custom loading message
              emptyTable: loading ? 'Loading EMs...' : 'No External Modules enabled',
                },
              }"
            class="table table-hover table-striped nowrap"
        >
          <thead>
          <tr>
            <th>Name</th>
            <th>Monthly Maintenance Cost</th>
          </tr>
          </thead>
          <div class="datatable-custom-footer mt-3">
            <p>Monthly Total: ${{ monthly_total }}</p>
          </div>
        </DataTable>
      </div>
    </div>


    <div v-if="this.redcapStep === 4" class="row d-flex d-inline-block">
      <div class="col-12 justify-content-center align-self-center">
        <h5
            class="d-inline-block  p-1"><i
            class="fa fa-check-circle"></i></h5> This project is properly configured with an approved
        REDCap
        Maintenance Agreement. Click <a target="_blank" :href="portalREDCapMaintenanceAgreement.link"><i
          class="fas fa-external-link-alt"></i> here</a> to access the SOW
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        The data for this table is refreshed daily. Recent changes may not be reflected in the table for 24
        hours. Refresh your project <a href="#">Feature in development.</a>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>