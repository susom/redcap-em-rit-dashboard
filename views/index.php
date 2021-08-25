<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */


try {


    ?>
    <!-- Add this to <head> -->

    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css"/>

    <!-- Load polyfills to support older browsers -->
    <script src="//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver"
            crossorigin="anonymous"></script>

    <!-- Load Vue followed by BootstrapVue -->
    <script src="//unpkg.com/vue@latest/dist/vue.min.js"></script>
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.js"></script>

    <!-- Load the following for BootstrapVueIcons support -->
    <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue-icons.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mdbvue/lib/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js"></script>

    <style>
        #user-tickets_processing {
            margin-top: 5% !important;
        }
    </style>


    <script src="<?php echo $module->getUrl('assets/js/index.js') ?>"></script>
    <script src="<?php echo $module->getUrl('assets/js/project_setup.js') ?>"></script>
    <script>
        //Main.ajaxCreateJiraTicketURL = "<?php //echo $module->getUrl('ajax/create_jira_ticket.php') ?>//"
        //Main.ajaxUserTicketURL = "<?php //echo $module->getUrl('ajax/get_user_tickets.php') ?>//"
        //Main.ajaxProjectEMstURL = "<?php //echo $module->getUrl('ajax/get_project_external_modules.php') ?>//"
        //Main.ajaxPortalProjectsListURL = "<?php //echo $module->getUrl('ajax/portal_project_list.php') ?>//"
        //Main.init()
    </script>
    <div id="app">
        <b-alert :variant="variant"
                 dismissible
                 fade
                 :show="showDismissibleAlert"
        >
            {{alertMessage}}
        </b-alert>
        <p v-html="header"></p>
        <div>
            <b-tabs content-class="mt-3">
                <b-tab title="Portal Linkage" active>
                    <?php
                    require("tabs/portal_linkage.php");
                    ?>
                </b-tab>
                <b-tab title="Tickets">
                    <?php
                    require("tabs/tickets.php");
                    ?>
                </b-tab>
                <b-tab title="External Modules">
                    <?php
                    require("tabs/external_modules.php");
                    ?>
                </b-tab>
            </b-tabs>
        </div>
        <?php
        require("modal.php");
        ?>
    </div>

    <script>
        new Vue({
            el: "#app",
            data() {
                return {
                    variant: "danger",
                    fields: ['id', 'title', 'type', 'status', 'created_at'],
                    filter: null,
                    currentPage: 1,
                    totalRows: 0,
                    perPage: 10,
                    items: [],
                    alertMessage: '',
                    fields_em: ['prefix', 'is_enabled', 'maintenance_fees'],
                    filter_em: null,
                    currentPage_em: 1,
                    totalRows_em: 0,
                    perPage_em: 10,
                    items_em: [],
                    showDismissibleAlert: false,
                    ticket: {
                        redcap_project_id: "<?php echo $module->getProjectId() ?>",
                        summary: "",
                        type: "",
                        description: "",
                        project_portal_id: "<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? $module->getPortal()->projectPortalSavedConfig['portal_project_id'] : '' ?>",
                    },
                    project_portal_id: "",
                    header: "<?php echo $module->getSystemSetting('rit-dashboard-main-header'); ?>",
                    ajaxCreateJiraTicketURL: "<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>",
                    ajaxUserTicketURL: "<?php echo $module->getUrl('ajax/get_user_tickets.php') ?>",
                    ajaxProjectEMstURL: "<?php echo $module->getUrl('ajax/get_project_external_modules.php') ?>",
                    ajaxPortalProjectsListURL: "<?php echo $module->getUrl('ajax/portal_project_list.php') ?>",
                    attachREDCapURL: "<?php echo $module->getURL('ajax/project_attach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    detachREDCapURL: "<?php echo $module->getURL('ajax/project_detach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    projectPortalSectionURL: "<?php echo $module->getURL('ajax/project_setup.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                }
            },
            methods: {
                linked: function () {
                    console.log('asdasdasdas')
                    if (this.ticket.project_portal_id !== '') {
                        return true;
                    }
                    return false;
                },
                onFiltered(filteredItems) {
                    // Trigger pagination to update the number of buttons/pages due to filtering
                    this.totalRows = filteredItems.length
                    this.currentPage = 1
                },
                onFilteredEM(filteredItems) {
                    // Trigger pagination to update the number of buttons/pages due to filtering
                    this.totalRows_em = filteredItems.length
                    this.currentPage_em = 1
                },
                prepareComponent: function () {
                    this.getUserTickets()
                    this.getProjectEMs()
                },
                getUserTickets: function () {
                    axios.get(this.ajaxUserTicketURL)
                        .then(response => {
                            this.items = response.data.data;
                            this.totalRows = this.items.length
                        });
                },
                getProjectEMs: function () {
                    axios.post(this.ajaxProjectEMstURL)
                        .then(response => {
                            this.items_em = response.data.data;
                            this.totalRows_em = this.items.length
                        });
                },
                submitTicket: function () {
                    console.log(this.ticket)
                    axios.post(this.ajaxCreateJiraTicketURL, this.ticket)
                        .then(response => {
                            this.getUserTickets()
                            this.$refs['generic-modal'].hide()
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                    ;
                },
                attachRedCapProject: function () {
                    const project = this.$refs.selectedProject
                    axios.post(this.attachREDCapURL, {
                        project_portal_id: this.ticket.project_portal_id,
                        project_portal_name: project.dataset.name,
                        project_portal_description: project.dataset.description
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.alertMessage = response.data.message
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                detachRedCapProject: function () {
                    axios.post(this.detachREDCapURL, {
                        project_portal_id: this.ticket.project_portal_id,
                        redcap_project_id: this.ticket.redcap_project_id
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.alertMessage = response.data.message
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                }
            },
            mounted() {
                this.prepareComponent();
            }
        });
    </script>
    <?php
} catch (\Exception $e) {
    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
}