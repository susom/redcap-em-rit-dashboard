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
                base_portal_url: this.$el.attributes.base_portal_url.value,
                project_status: this.$el.attributes.project_status.value,
                portal_linkage_header: this.$el.attributes.portal_linkage_header.value,
                tickets_header: this.$el.attributes.tickets_header.value,
                external_modules_header: this.$el.attributes.external_modules_header.value,
                portal_projects_list: JSON.parse(this.$el.attributes.portal_projects_list.value),
                saved_portal_project_object: JSON.parse(this.$el.attributes.saved_portal_project_object.value),
            }
        })
    }
})