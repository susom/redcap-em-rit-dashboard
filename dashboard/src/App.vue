<template>
  <div id="app" class="container-fluid height-100">
    <b-alert :variant="noneDismissibleVariant"
             fade
             :show="showNoneDismissibleAlert"
    >
      <b-row>
        <b-col class="justify-content-center align-self-center" lg="1" style="font-size: 40px"><i
            class="fas fa-exclamation-circle"></i></b-col>
        <b-col class="justify-content-center align-self-center" lg="11"><p
            v-html="noneDismissibleAlertMessage"></p></b-col>

      </b-row>
    </b-alert>
    <b-alert :variant="this.alertVariant"
             dismissible
             fade
             :show="this.alertShow"
    >
      <b class="row" v-html="this.alertMessage"></b>
    </b-alert>
    <div class="text-left">
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
    <b-overlay :show="this.isLoading" variant="light" opacity="0.80" rounded="sm">
      <div>
        <b-tabs>
          <b-tab title="R2P2" active>
            <r2p2 @globalMessage="updateGlobalAlertMessage"
                  @noneDismissableGlobalMessage="updateNoneDismissibleGlobalAlertMessage"
                  @updateProjectLinkedProp="updateProjectLinkedProp"
                  @updateProjectR2P2Object="updateProjectR2P2Object"
                  @determineREDCapStep="determineREDCapStep"
                  @setGlobalProjectPortalREDCapMaintenanceAgreement="setGlobalProjectPortalREDCapMaintenanceAgreement"
                  :redcap_project_id="this.pid"
                  :ajaxAppendSignedAuthURL="this.ajaxAppendSignedAuthURL"
                  :portalREDCapMaintenanceAgreement="this.portalREDCapMaintenanceAgreement" :items_em="this.items_em"
                  :attachREDCapURL="this.attachREDCapURL" :ajaxGenerateSignedAuthURL="this.ajaxGenerateSignedAuthURL"
                  :portal_project_object="this.portal_project_object" :portal_projects_list="this.portal_projects_list"
                  :linked="this.linked" :totalFees="this.totalFees" :projectStep="this.projectStep"
                  :notifications="this.notifications" :portal_linkage_header="this.portal_linkage_header"></r2p2>
          </b-tab>
          <b-tab title="Support">
            <support @globalMessage="updateGlobalAlertMessage"
                     @noneDismissableGlobalMessage="updateNoneDismissibleGlobalAlertMessage"
                     :ajaxCreateJiraTicketURL="this.ajaxCreateJiraTicketURL"
                     :base_portal_url="this.base_portal_url" :isLoading="this.isLoading"
                     :ajaxUserTicketURL="this.ajaxUserTicketURL" :tickets_header="this.tickets_header"
                     :isDisabled="this.isDisabled" :portal_projects_list="this.portal_projects_list"></support>
          </b-tab>
          <b-tab title="External Modules">
            <external-modules @globalMessage="updateGlobalAlertMessage"
                              @noneDismissibleGlobalMessage="updateNoneDismissibleGlobalAlertMessage"
                              @setGlobalProjectStep="setGlobalProjectStep"
                              @determineREDCapStep="determineREDCapStep"
                              @setGlobalProjectTotalFees="setGlobalProjectTotalFees"
                              @setGlobalProjectPortalREDCapMaintenanceAgreement="setGlobalProjectPortalREDCapMaintenanceAgreement"
                              @setGlobalProjectExternalModules="setGlobalProjectExternalModules"
                              :project_status="this.project_status" :ajaxGetSignedAuthURL="this.ajaxGetSignedAuthURL"
                              v-bind:linked="this.linked"
                              :projectStep="this.projectStep"
                              :ajaxProjectEMstURL="this.ajaxProjectEMstURL"
                              :external_modules_header="this.external_modules_header"
                              :notifications="this.notifications"></external-modules>
          </b-tab>
        </b-tabs>
      </div>
    </b-overlay>
    <p>
      <i>We appreciate your patience -- R2P2 is new and we look forward to
        <a href="https://redcap.stanford.edu/surveys/?s=DNFNAKARFTELJ4WA" target="_blank">
          incorporating your feedback
        </a> to make it better! <a href=""></a></i>
    </p>
  </div>
</template>

<script>
import Vue from 'vue'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
import notificationsFile from '../../language/Notifications.ini'
import axios from "axios";
// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vue-select/dist/vue-select.css';
// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

import r2p2 from "@/components/tabs/r2p2";
import support from "@/components/tabs/support";
import External_modules from "@/components/tabs/external_modules";

var ajaxCalls = []
export default {
  name: 'App',
  components: {
    "r2p2": r2p2,
    "support": support,
    "external-modules": External_modules
  },
  data() {
    return {
      projectStep: 0,
      isLoading: true,
      isDisabled: true,
      notifications: {},
      alertShow: false,
      alertVariant: "danger",
      alertMessage: "bolbol",
      noneDismissibleVariant: "danger",
      showNoneDismissibleAlert: false,
      noneDismissibleAlertMessage: "",
      portalREDCapMaintenanceAgreement: {},
      items_em: [],
      linked: false,
      totalFees: 0,
      portal_project_object: {},
    }
  },
  created() {
    axios.interceptors.request.use((config) => {
      // trigger 'loading=true' event here
      ajaxCalls.push(config)
      if (this.isLoading !== undefined) {
        this.isLoading = true
      }
      this.isDisabled = true
      return config;
    }, (error) => {
      // trigger 'loading=false' event here
      this.isLoading = false
      return Promise.reject(error);
    });

    axios.interceptors.response.use((response) => {
      // trigger 'loading=false' event here
      var temp = []
      temp = ajaxCalls.pop()
      if (ajaxCalls.length === 0) {
        this.isLoading = false
      }
      this.isDisabled = false
      return response;
    }, (error) => {
      // trigger 'loading=false' event here
      this.isLoading = false
      return Promise.reject(error);
    });
  },
  methods: {
    isLinked: function () {
      try {
        if (this.portal_project_object.portal_project_id !== undefined && this.portal_project_object.portal_project_id !== '') {
          this.linked = true;
        } else {
          this.linked = false;
        }
      } catch (e) {
        console.log(e)
      }
    },
    manupilateProjectInfo: function () {
      var element = document.getElementById('subheaderDiv2')
      element.style.float = "right"
      element.className = ''
      var navs = document.getElementsByClassName('navbar-brand')
      navs[0].style.display = 'none'
    },
    updateGlobalAlertMessage: function (variant, message, show) {
      // Portal Linkage tab alert message
      this.alertMessage = message
      this.alertShow = show
      this.alertVariant = variant
    },
    updateNoneDismissibleGlobalAlertMessage: function (variant, message, show) {
      // Portal Linkage tab alert message
      this.noneDismissibleAlertMessage = message
      this.showNoneDismissibleAlert = show
      this.noneDismissibleVariant = variant
    },
    setGlobalProjectStep: function (step) {
      this.projectStep = step
    },
    updateProjectLinkedProp: function (value) {
      this.linked = value
    },
    updateProjectR2P2Object: function (obj) {
      this.portal_project_object = obj
    },
    setGlobalProjectTotalFees: function (fees) {
      this.totalFees = fees
    },
    hasREDCapMaintenanceAgreement: function () {
      //if (this.linked() == true && this.hasManagePermission == true && this.portalREDCapMaintenanceAgreement.project_id == undefined) {
      if (this.portalREDCapMaintenanceAgreement.project_id == undefined) {
        return false
      }
      return true;
    },
    determineREDCapStep: function () {
      var step = 1
      if (this.hasREDCapMaintenanceAgreement() === false) {
        step = 1
      } else {
        /**
         APPROVED_PENDING_DEVELOPMENT = 2
         APPROVED_ACTIVE_DEVELOPMENT = 6
         APPROVED_MAINTENANCE = 7
         * @type {number[]}
         */
        var statsus = [2, 6, 7]
        // if redcap parameter exists RMA created but current redcap is not part of it.
        if (this.portalREDCapMaintenanceAgreement.redcap !== undefined) {
          step = 2
        } else if (!statsus.includes(this.portalREDCapMaintenanceAgreement.sow_status)) {
          step = 3
        } else if (statsus.includes(this.portalREDCapMaintenanceAgreement.sow_status)) {
          step = 4
        }
        //step = 5
      }
      this.projectStep = step
      console.log(this.portalREDCapMaintenanceAgreement)
      console.log('project step: ' + step)
      // pass step variable to other components.
      return step
    },
    setGlobalProjectPortalREDCapMaintenanceAgreement: function (obj) {
      console.log(obj)
      this.portalREDCapMaintenanceAgreement = obj
    },
    setGlobalProjectExternalModules: function (arr) {
      this.items_em = arr
    },
    prepareComponent: function () {
      try {
        this.notifications = this.parseINIString(notificationsFile)
        this.portal_project_object = this.saved_portal_project_object
      } catch (e) {
        console.log(e);
      }
    },
    parseINIString: function (data) {
      var regex = {
        section: /^\s*\[\s*([^\]]*)\s*\]\s*$/,
        param: /^\s*([^=]+?)\s*=\s*(.*?)\s*$/,
        comment: /^\s*;.*$/
      };
      var value = {};
      var lines = data.split(/[\r\n]+/);
      var section = null;
      var match = ''
      lines.forEach(function (line) {
        if (regex.comment.test(line)) {
          return;
        } else if (regex.param.test(line)) {
          match = line.match(regex.param);
          if (section) {
            value[section][match[1]] = match[2];
          } else {
            var v = match[2]
            value[match[1]] = v.slice(1, -1);
          }
        } else if (regex.section.test(line)) {
          match = line.match(regex.section);
          value[match[1]] = {};
          section = match[1];
        } else if (line.length == 0 && section) {
          section = null;
        }
      });
      return value;
    }
  },
  props: {
    pid: String,
    ajaxCreateJiraTicketURL: String,
    ajaxUserTicketURL: String,
    ajaxProjectEMstURL: String,
    ajaxGenerateSignedAuthURL: String,
    ajaxAppendSignedAuthURL: String,
    ajaxGetSignedAuthURL: String,
    ajaxPortalProjectsListURL: String,
    attachREDCapURL: String,
    detachREDCapURL: String,
    projectPortalSectionURL: String,
    base_portal_url: String,
    project_status: String,
    portal_linkage_header: String,
    tickets_header: String,
    external_modules_header: String,
    portal_projects_list: Array,
    saved_portal_project_object: Object,
  },
  mounted() {
    this.prepareComponent();
    this.isLinked()
    this.manupilateProjectInfo()
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

#user-tickets_processing {
  margin-top: 5% !important;
}

a:hover {
  text-decoration: none !important;
}

.height-100 {
  min-height: 100vh !important;
}

.tab-content {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  padding: 10px;
  min-height: 30vh;
}

.nav-link {
  font-weight: bold;
}

.nav-link .active {
  color: blue;
  font-weight: bold;
}

.nav-tabs {
  margin-bottom: 0;
}

.center-block {
  display: table; /* Instead of display:block */
  margin-left: auto;
  margin-right: auto;
}

#user-tickets_processing {
  margin-top: 5% !important;
}

a:hover {
  text-decoration: none !important;
}

.height-100 {
  min-height: 100vh !important;
}

.tab-content {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  padding: 10px;
  min-height: 30vh;
}

.nav-link {
  font-weight: bold;
}

.nav-link .active {
  color: blue;
  font-weight: bold;
}

.nav-tabs {
  margin-bottom: 0;
}

.center-block {
  display: table; /* Instead of display:block */
  margin-left: auto;
  margin-right: auto;
}

.nopadding {
  padding: 0 !important;
  margin: 0 !important;
}

.center-list {
  width: 90%;
  /* display: block !important; */
  margin-left: auto;
  margin-right: 0;
}

.btn-contact-admin {
  background-color: #337ab7 !important;
}
</style>
