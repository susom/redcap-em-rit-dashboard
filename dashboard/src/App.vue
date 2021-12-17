<template>
  <div id="app" class="container">
    <b-alert :variant="this.alertVariant"
             dismissible
             fade
             :show="this.alertShow"
    >
      <b class="row" v-html="this.alertMessage"></b>
    </b-alert>
    <b-overlay :show="this.isLoading" variant="light" opacity="0.80" rounded="sm">
      <h1>{{ this.pid }}</h1>
      <div>
        <b-tabs content-class="mt-3">
          <b-tab title="R2P2" active>
            <r2p2></r2p2>
          </b-tab>
          <b-tab title="Support">
            <support @globalMessage="updateGlobalAlertMessage" :ajaxCreateJiraTicketURL="this.ajaxCreateJiraTicketURL"
                     :base_portal_url="this.base_portal_url" :isLoading="this.isLoading"
                     :ajaxUserTicketURL="this.ajaxUserTicketURL" :tickets_header="this.tickets_header"
                     :isDisabled="this.isDisabled" :portal_projects_list="this.portal_projects_list"></support>
          </b-tab>
          <b-tab title="External Modules">
            <external-modules></external-modules>
          </b-tab>
        </b-tabs>
      </div>
      <test></test>
    </b-overlay>
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
      isLoading: true,
      isDisabled: true,
      notifications: {},
      alertShow: false,
      alertVariant: "danger",
      alertMessage: "bolbol"
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
    updateGlobalAlertMessage: function (variant, message, show) {
      // Portal Linkage tab alert message
      this.alertMessage = message
      this.alertShow = show
      this.alertVariant = variant
    },
    prepareComponent: function () {
      // try {
      //   this.notifications = this.parseINIString(notificationsFile)
      //   console.log(this.notifications);
      //   console.log(this.notifications.append_rma_success_message);
      // } catch (e) {
      //   console.log(e);
      // }
      // var notifications = loadIniFile.sync(this.notificationsURL)

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
            value[match[1]] = match[2];
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
  },
  mounted() {
    this.prepareComponent();
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
</style>
