<template>
  <div id="app" class="container">

    <b-overlay :show="this.isLoading" variant="light" opacity="0.80" rounded="sm">
      <h1>{{ this.pid }}</h1>
      <tabs></tabs>
      <test></test>
    </b-overlay>
  </div>
</template>

<script>
import Vue from 'vue'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

import Test from './components/Test'
import Tabs from './components/tabs'

export default {
  name: 'App',
  components: {
    'test': Test,
    'tabs': Tabs
  },
  data() {
    return {
      isLoading: true
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
