import Vue from 'vue'
import App from './App.vue'

Vue.config.productionTip = false
Vue.config.devtools = true
new Vue({
    el: 'app',
    render(h) {
        return h(App, {
            props: {
                pid: this.$el.attributes.pid.value,
                ajaxCreateJiraTicketURL: this.$el.attributes.ajaxCreateJiraTicketURL.value,
                ajaxUserTicketURL: this.$el.attributes.ajaxUserTicketURL.value,
                ajaxProjectEMstURL: this.$el.attributes.ajaxProjectEMstURL.value,
                ajaxGenerateSignedAuthURL: this.$el.attributes.ajaxGenerateSignedAuthURL.value,
                ajaxAppendSignedAuthURL: this.$el.attributes.ajaxAppendSignedAuthURL.value,
                ajaxGetSignedAuthURL: this.$el.attributes.ajaxGetSignedAuthURL.value,
                ajaxPortalProjectsListURL: this.$el.attributes.ajaxPortalProjectsListURL.value,
                attachREDCapURL: this.$el.attributes.attachREDCapURL.value,
                detachREDCapURL: this.$el.attributes.detachREDCapURL.value,
                projectPortalSectionURL: this.$el.attributes.projectPortalSectionURL.value,
            }
        })
    }
})