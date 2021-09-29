<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */


try {


    ?>
    <!-- Add this to <head> -->

    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <!--    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>-->
    <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.min.css"/>

    <!-- Load polyfills to support older browsers -->
    <script src="//polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver"
            crossorigin="anonymous"></script>

    <!-- Load Vue followed by BootstrapVue -->
    <!--    <script src="//unpkg.com/vue@latest/dist/vue.min.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>

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

        .nav-tabs {
            margin-bottom: 0;
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
        <b-overlay :show="isLoading" variant="secondary" opacity="0.80" rounded="sm">
            <b-container fluid class="height-100">

                <b-alert :variant="noneDismissibleVariant"
                         fade
                         :show="showNoneDismissibleAlert"
                ><i class="fas fa-exclamation-circle"></i>
                    {{noneDismissibleAlertMessage}}
                </b-alert>
                <b-alert :variant="variant"
                         dismissible
                         fade
                         :show="showDismissibleAlert"
                >
                    {{alertMessage}}
                </b-alert>
                <p v-html="header"></p>
                <div class="mt-3">
                    <b-tabs content-class="">
                        <b-tab title="Research IT Portal" active>
                            <?php
                            require("tabs/portal_linkage.php");
                            ?>
                        </b-tab>
                        <b-tab title="Support Tickets">
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

            </b-container>
        </b-overlay>
        <!--        <my-component-name></my-component-name>-->
        <?php
        require("modal.php");
        ?>
    </div>

    <script type="module">
        var ajaxCalls = []
        //import MyComponent from "<?php echo $module->getUrl('views/tabs/Test.vue', false, true) ?>"

        //Vue.component('my-component-name', MyComponent)
        new Vue({
            el: "#app",
            data() {
                return {
                    variant: "danger",
                    noneDismissibleVariant: "danger",
                    portalLinkageVariant: "danger",
                    EMVariant: "danger",
                    // fields: ['id', 'title', 'type', 'status', 'created_at', 'for_current_pid'],
                    fields: [
                        {
                            key: 'id',
                            sortable: true
                        },
                        {
                            key: 'title',
                            sortable: true
                        },
                        {
                            key: 'type',
                            sortable: true
                        },
                        {
                            key: 'status',
                            sortable: true
                        },
                        {
                            key: 'created_at',
                            sortable: true
                        },
                        {
                            key: 'for_current_pid',
                            sortable: true
                        }
                    ],
                    filter: null,
                    currentPage: 1,
                    totalRows: 0,
                    perPage: 10,
                    items: [],
                    allItems: [],
                    alertMessage: '',
                    portalLinkageAlertMessage: '',
                    EMAlertMessage: '',
                    noneDismissibleAlertMessage: '',
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
                    filter_em: null,
                    currentPage_em: 1,
                    totalRows_em: 0,
                    perPage_em: 10,
                    items_em: [],
                    totalFees: 0,
                    showDismissibleAlert: false,
                    showNoneDismissibleAlert: false,
                    showPortalLinkageDismissibleAlert: false,
                    showEMDismissibleAlert: false,
                    ticket: {
                        redcap_project_id: "<?php echo $module->getProjectId() ?>",
                        summary: "",
                        type: "",
                        description: "",
                        project_portal_id: "<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? $module->getPortal()->projectPortalSavedConfig['portal_project_id'] : '' ?>",
                        project_portal_name: "<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_name']) ? $module->getPortal()->projectPortalSavedConfig['portal_project_name'] : '' ?>",
                        project_portal_id_saved: "<?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) ? "true" : "false" ?>",
                        project_portal_url: "<?php echo $module->getClient()->getPortalBaseURL() . 'detail/' . $module->getPortal()->projectPortalSavedConfig['portal_project_id'] ?>"
                    },
                    project_status: "<?php echo $module->getProject()->project['status'] ?>",
                    portal_projects_list: <?php echo json_encode($module->getUser()->getProjectPortalList()) ?>,
                    ticket_types: <?php echo json_encode($module->getSupport()->getJiraIssueTypes()) ?>,
                    project_portal_id: "",
                    header: "<?php echo str_replace(array("\n", "\r", "\""), array("\\n", "\\r", ""), $module->getSystemSetting('rit-dashboard-main-header'));; ?>",
                    ajaxCreateJiraTicketURL: "<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>",
                    ajaxUserTicketURL: "<?php echo $module->getUrl('ajax/get_user_tickets.php') ?>",
                    ajaxProjectEMstURL: "<?php echo $module->getUrl('ajax/get_project_external_modules.php') ?>",
                    ajaxGenerateSignedAuthURL: "<?php echo $module->getUrl('ajax/generate_signed_auth.php') ?>",
                    ajaxAppendSignedAuthURL: "<?php echo $module->getUrl('ajax/append_approved_signed_auth.php') ?>",
                    ajaxGetSignedAuthURL: "<?php echo $module->getUrl('ajax/get_signed_auth.php') ?>",
                    ajaxPortalProjectsListURL: "<?php echo $module->getUrl('ajax/portal_project_list.php') ?>",
                    attachREDCapURL: "<?php echo $module->getURL('ajax/project_attach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    detachREDCapURL: "<?php echo $module->getURL('ajax/project_detach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    projectPortalSectionURL: "<?php echo $module->getURL('ajax/project_setup.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    portal_linkage_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-portal-linkage-tab-header')); ?>",
                    tickets_header: '<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-ticket-tab-header')); ?>',
                    external_modules_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-external-modules-tab-header')); ?>",
                    hasManagePermission: "<?php echo $module->getUser()->isUserHasManagePermission(); ?>",
                    portalSignedAuth: [],
                    refCount: 0,
                    isLoading: true,
                    currentProjectTickets: 'Yes',
                    emptyTicketsTable: "No Tickets Found",
                    emptyFilteredTicketsTable: "No tickets attached to this REDCap Project. To See full list uncheck 'Display Tickets for current REDCap projects' checkbox"
                }
            },
            created() {
                axios.interceptors.request.use((config) => {
                    // trigger 'loading=true' event here
                    ajaxCalls.push(config)
                    if (this.isLoading != undefined) {
                        this.isLoading = true
                    }

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
                    return response;
                }, (error) => {
                    // trigger 'loading=false' event here
                    this.isLoading = false
                    return Promise.reject(error);
                });
            },
            methods: {
                setPortalLinkageAlertMessage: function (variant, message, show) {
                    // Portal Linkage tab alert message
                    this.portalLinkageAlertMessage = message
                    this.showPortalLinkageDismissibleAlert = show
                    this.portalLinkageVariant = variant
                },
                setEMAlertMessage: function (variant, message, show) {
                    // EM tab alert message
                    this.EMAlertMessage = message
                    this.showEMDismissibleAlert = show
                    this.EMVariant = variant
                },
                hasSignedAuthorization: function () {
                    if (this.linked() == true && this.hasManagePermission == true && this.portalSignedAuth.project_id == undefined) {
                        return false
                    }
                    return true;
                },
                linked: function () {
                    if (this.ticket.project_portal_id !== '' && this.ticket.project_portal_id_saved === "true") {
                        return true;
                    }
                    return false;
                },
                filterTickets() {
                    if (this.currentProjectTickets === 'Yes') {
                        this.items = this.allItems.filter(function (n) {
                            return n.for_current_pid === 'Yes';
                        });
                    } else {
                        this.items = this.allItems
                    }

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
                    // only try to get signed auth if project is linked
                    if (this.linked()) {
                        this.getSignedAuth()
                    } else {
                        // global alert message that is none dismissible
                        if (this.project_status == "0") {
                            this.showNoneDismissibleAlert = true
                            this.noneDismissibleAlertMessage += "This REDCap project uses External Modules that require a monthly maintenance subscription.  Please link this REDCap project to the Research IT Portal and complete the REDCap External Module Maintenance Approval process to maintain use of the External Modules."
                            this.noneDismissibleVariant = "danger"
                        }

                        // EM tab alert message
                        //this.setPortalLinkageAlertMessage("danger", "You must first link this REDCap project to the Research IT Portal.  Please click on the Research IT Portal tab to continue.", true)
                    }
                },
                getUserTickets: function () {
                    this.emptyTicketsTable = 'No Tickets for this REDCap project'
                    axios.get(this.ajaxUserTicketURL)
                        .then(response => {
                            this.items = this.allItems = response.data.data;
                            this.totalRows = this.items.length
                            if (this.items.length == undefined || this.items.length == 0) {
                                this.emptyTicketsTable = 'No Tickets Found'
                            }
                            this.filterTickets()
                        });
                },
                getProjectEMs: function () {
                    axios.post(this.ajaxProjectEMstURL)
                        .then(response => {
                            if (response.data.data != undefined) {
                                this.items_em = response.data.data;
                                this.totalRows_em = this.items_em.length;
                                for (var i = 0; i < this.items_em.length; i++) {
                                    this.totalFees += parseFloat(this.items_em[i].maintenance_fees)
                                }
                            }
                        }).then(() => {
                        if (this.hasSignedAuthorization() === false) {
                            // project in dev mode but has EM with monthly fees
                            if (this.totalFees > 0 && this.project_status == "0") {
                                if (this.linked() === false) {
                                    this.setEMAlertMessage("warning", "Prior to moving this project to production mode, you will first need to associate it with a Resaerch IT Portal project and ensure you have an active REDCap External Module Maintenance agreement in place.  See the Portal tab for next steps.", true)
                                } else {
                                    this.setEMAlertMessage("warning", "Prior to moving this project to production mode, you will first need ensure you have an active REDCap External Module Maintenance agreement in place.  See the Portal tab for next steps.", true)
                                }


                                // project in prod mode but has EM with monthly fees
                            }
                            if (this.totalFees > 0 && this.project_status == "1") {
                                this.setEMAlertMessage("danger", "This project uses External Modules that require a REDCap External Module Maintenance agreement.  Please complete the required steps on the Portal Tab or the external modules may be deactivated.", true)
                                // project in analysis mode but has EM with monthly fees
                            }
                            if (this.totalFees > 0 && this.project_status == "2") {
                                this.setEMAlertMessage("info", "The external module maintenance costs only apply while a project is in Production mode.  The current fees are on-hold will not be charged while the project is in analysis/archival mode.", true)
                            }
                        }
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
                    var project = []
                    for (var i = 0; i < this.portal_projects_list.length; i++) {
                        if (this.portal_projects_list[i].id == this.ticket.project_portal_id) {
                            project = this.portal_projects_list[i]
                        }
                    }
                    console.log(project)
                    axios.post(this.attachREDCapURL, {
                        project_portal_id: project.id,
                        project_portal_name: project.project_name,
                        project_portal_description: project.project_description
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.ticket.project_portal_id_saved = "true"
                            this.ticket.project_portal_name = project.project_name
                            this.ticket.project_portal_id = project.id
                            this.showNoneDismissibleAlert = false
                            this.alertMessage = response.data.message
                            this.ticket.project_portal_url = response.data.portal_project.portal_project_url
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
                },
                generateSignedAuth: function () {
                    axios.post(this.ajaxGenerateSignedAuthURL, {
                        project_portal_id: this.ticket.project_portal_id,
                        redcap_project_id: this.ticket.redcap_project_id,
                        external_modules: this.items_em
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.showPortalLinkageDismissibleAlert = false
                            this.showEMDismissibleAlert = false
                            this.alertMessage = response.data.message
                            this.portalSignedAuth = response.data;
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                appendSignedAuth: function () {
                    axios.post(this.ajaxAppendSignedAuthURL, {
                        project_portal_id: this.ticket.project_portal_id,
                        redcap_project_id: this.ticket.redcap_project_id,
                        portal_sow_id: this.portalSignedAuth.id,
                        external_modules: this.items_em
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.showPortalLinkageDismissibleAlert = false
                            this.showEMDismissibleAlert = false
                            this.alertMessage = response.data.message
                            this.portalSignedAuth = response.data;
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                getSignedAuth: function () {
                    axios.get(this.ajaxGetSignedAuthURL)
                        .then(response => {
                            this.portalSignedAuth = response.data;
                            if (this.hasSignedAuthorization() === false) {
                                this.setPortalLinkageAlertMessage("warning", "In order to use certain External Modules in this REDCap project, authorize the monthly maintenance for the Research IT Portal Project ", true)
                                this.setEMAlertMessage("warning", "In order to use certain External Modules in this REDCap project, authorize the monthly maintenance for the Research IT Portal Project ", true)

                            } else if (this.portalSignedAuth.redcap != undefined) {
                                this.setPortalLinkageAlertMessage("warning", "This REDCap project has not yet been linked to an approved REDCap External Module Maintenance Agreement.  Please click here to authorize this REDCap project to use the approved maintenance agreement.  The project owner(s) will be notified by email.", true)

                            } else if (this.portalSignedAuth.sow_status == "0") {
                                this.setPortalLinkageAlertMessage("warning", "Your REDCap Maintenance Agreement is pending approval.  Please have someone with a valid PTA complete the agreement and authorize this project for External Module maintenance.  You can add additional users (such as a finance administrator) to the Research IT Portal if you are unable to authorize the agreement yourself.", true)

                            }
                        });
                },
                getREDCapProjectsNames: function () {
                    var projects = ''

                    for (var i = 0; i < this.portalSignedAuth.redcap.length; i++) {
                        projects += this.portalSignedAuth.redcap[i]['redcap_project_name'] + ','

                    }
                    return projects.substring(0, projects.length - 1);
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