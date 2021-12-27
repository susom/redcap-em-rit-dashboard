<template>
  <div>
    <b-container fluid>
      <b-row>
        <b-alert :variant="EMVariant"
                 dismissible
                 fade
                 :show="showEMDismissibleAlert"
        ><i class="fas fa-exclamation-circle"></i>
          {{ EMAlertMessage }}
        </b-alert>
      </b-row>
      <div class="row">
        <span v-html="external_modules_header"></span>
      </div>

      <b-table striped hover :items="items_em" :fields="fields_em" :current-page="currentPage_em"
               :per-page="perPage_em"
               :filter="filter_em" @filtered="onFilteredEM">
        <!--    <template #cell(id)="data">-->
        <!--        <span v-html="data.value"></span>-->
        <!--    </template>-->

        <template #thead-top="data">
          <span v-html="data.value"></span>
          <b-tr style="background-color: #D7D7D7">
            <b-th>
              <b-row>
                <b-col lg="12">
                  <b-form-checkbox v-model="currentProjectEms"
                                   name="all-ems"
                                   id="all-ems"
                                   value="Yes"
                                   unchecked-value="No" @change="filterEms($event)">Show only External Modules
                    with Fees
                  </b-form-checkbox>

                </b-col>
              </b-row>
            </b-th>
            <b-th>
              <b-form-group
                  label-for="filter-input"
                  label-size="sm"
                  class="mb-0"
              >
                <b-input-group size="sm">
                  <b-form-input
                      id="filter-input"
                      v-model="filter_em"
                      type="search"
                      placeholder="Type to Search"
                  ></b-form-input>

                  <b-input-group-append>
                    <b-button size="sm" :disabled="!filter_em" @click="filter_em = ''">Clear</b-button>
                  </b-input-group-append>
                </b-input-group>
              </b-form-group>
            </b-th>
          </b-tr>
        </template>
        <!-- render html for this column -->
        <template #cell(maintenance_monthly_cost)="data">
          <span v-html="data.value"></span>
        </template>
      </b-table>
      <b-row>

      </b-row>
      <b-row class="mt-2">
        <b-col class="float-left" md="3">
          Monthly Total: <strong>${{ totalFees }}</strong>
        </b-col>
        <b-col offset="3" sm="6" md="6" class="my-1">
          <b-pagination
              v-model="currentPage_em"
              :total-rows="totalRows_em"
              :per-page="perPage_em"
              align="fill"
              size="sm"
              class="my-0"
          ></b-pagination>
        </b-col>
      </b-row>
      <b-alert v-if="this.projectStep === 4" class="d-flex d-inline-block"
               variant="success"
               fade
               show
      >
        <b-row class="mt-2">

          <b-col class="justify-content-center align-self-center" lg="12"><h5
              class="d-inline-block  p-1"><i
              class="fa fa-check-circle"></i></h5> This project is properly configured with an approved
            REDCap
            Maintenance Agreement. Click <a target="_blank" :href="portalREDCapMaintenanceAgreement.link"><i
                class="fas fa-external-link-alt"></i> here</a> to access the SOW
          </b-col>
        </b-row>
      </b-alert>

      <b-row>
        <b-col>The data for this table is refreshed daily. Recent changes may not be reflected in the table for 24
          hours. Refresh your project <a href="#">Feature in development.</a></b-col>
      </b-row>

    </b-container>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "external_modules",
  data() {
    return {
      totalFees: 0,
      currentProjectEms: 'Yes',
      EMVariant: "danger",
      data: [],
      showEMDismissibleAlert: false,
      EMAlertMessage: "",
      filter_em: null,
      currentPage_em: 1,
      totalRows_em: 0,
      perPage_em: 100,
      items_em: [],
      portalREDCapMaintenanceAgreement: {},
      allEms: [],
      fields_em: [
        {
          key: 'prefix',
          label: 'Name',
          sortable: true
        },
        {
          key: 'maintenance_monthly_cost',
          label: 'Monthly Maintenance Cost',
          sortable: true
        }
      ],
    }
  },
  methods: {
    onFilteredEM(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRows_em = filteredItems.length
      this.currentPage_em = 1
    },
    filterEms() {
      if (this.currentProjectEms === 'Yes') {
        this.items_em = this.allEms.filter(function (n) {
          return n.maintenance_fees > 0;
        });
      } else {
        this.items_em = this.allEms
      }

    },
    getSignedAuth: function () {
      axios.get(this.ajaxGetSignedAuthURL + '&monthly_payment=' + this.totalFees)
          .then(response => {
            this.$emit('setGlobalProjectPortalREDCapMaintenanceAgreement', response.data)
            if (this.projectStep === 1) {
              this.emTabAlerts()
            }
          }).then(response => {
        //this.determineREDCapStep()
        this.$emit('determineREDCapStep')
      });
    },
    setEMAlertMessage: function (variant, message, show) {
      // EM tab alert message
      this.EMAlertMessage = message
      this.showEMDismissibleAlert = show
      this.EMVariant = variant
    },
    getProjectEMs: function () {
      axios.post(this.ajaxProjectEMstURL)
          .then(response => {
            if (response.data.data != undefined) {
              this.items_em = this.allEms = response.data.data;

              this.$emit('setGlobalProjectExternalModules', this.items_em)
              this.totalRows_em = this.items_em.length;
              for (var i = 0; i < this.items_em.length; i++) {
                this.totalFees += parseFloat(this.items_em[i].maintenance_fees)
              }
              this.filterEms()
            }
          }).then(() => {
        // only try to get signed auth if project is linked
        if (this.linked) {
          return this.getSignedAuth()
        } else {
          if (this.projectStep === 1) {
            this.emTabAlerts()
          }
          // global alert message that is none dismissible
          if (this.project_status === "0" && this.totalFees > 0) {
            // Dev-mode redcap project
            // this.showNoneDismissibleAlert = true
            // this.noneDismissibleAlertMessage += this.notifications.get_project_ems_dev
            // this.noneDismissibleVariant = "warning"
            this.$emit('noneDismissibleGlobalMessage', 'warning', this.notifications.get_project_ems_dev, true)
          } else if (this.project_status === "1" && this.totalFees > 0) {
            // Production mode redcap project
            // this.showNoneDismissibleAlert = true
            // this.noneDismissibleAlertMessage += this.notifications.get_project_ems_prod
            // this.noneDismissibleVariant = "danger"
            var notification = this.replaceNotificationsVariables(this.notifications.get_project_ems_prod, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})

            this.$emit('noneDismissibleGlobalMessage', 'danger', notification, true)

          }
        }
      });
    },
    emTabAlerts: function () {
      // project in dev mode but has EM with monthly fees
      if (this.totalFees > 0 && this.project_status === "0") {
        this.setEMAlertMessage("warning", this.notifications.get_project_ems_dev_2, true)
        // project in prod mode but has EM with monthly fees
      }
      if (this.totalFees > 0 && this.project_status === "1") {
        var notification = this.replaceNotificationsVariables(this.notifications.get_project_ems_prod_2, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})

        this.setEMAlertMessage("danger", notification, true)
        // project in analysis mode but has EM with monthly fees
      }
      if (this.totalFees > 0 && this.project_status === "2") {
        this.setEMAlertMessage("info", this.notifications.get_project_ems_analysis, true)
      }
    }
  },
  mounted() {
    this.getProjectEMs();
  },
  props: {
    external_modules_header: String,
    ajaxProjectEMstURL: String,
    ajaxGetSignedAuthURL: String,
    project_status: String,
    projectStep: Number,
    linked: Boolean,
    notifications: Object,
  }
}
</script>

<style scoped>

</style>