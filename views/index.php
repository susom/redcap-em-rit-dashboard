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
    <script src="https://unpkg.com/vue-select@latest"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@latest/dist/vue-select.css">

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
    </style>


    <script src="<?php echo $module->getUrl('assets/js/index.js') ?>"></script>
    <script src="<?php echo $module->getUrl('assets/js/project_setup.js') ?>"></script>
    <div id="app">

        <b-container fluid class="height-100">

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
            <b-alert :variant="variant"
                     dismissible
                     fade
                     :show="showDismissibleAlert"
            >
                <b v-html="alertMessage"></b>
            </b-alert>
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

            <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
                <div class="mt-3">
                    <b-tabs content-class="">
                        <b-tab title="R2P2" active>
                            <?php
                            require("tabs/portal_linkage.php");
                            ?>
                        </b-tab>
                        <b-tab title="Support Tickets">
                            <?php
                            require("tabs/tickets.php");
                            ?>
                        </b-tab>
                        <b-tab title="Enabled External Modules">
                            <?php
                            require("tabs/external_modules.php");
                            ?>
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
        </b-container>

        <!--        <Navigation></Navigation>-->
        <?php
        require("modal.php");
        ?>
    </div>
    <!--    <script src="--><?php //echo $module->getUrl('views/tabs/Test.vue.js', false, false) ?><!--"></script>-->
    <script type="module">
        var ajaxCalls = []
        Vue.component('v-select', VueSelect.VueSelect);
        // Vue.component('Navigation', Navigation)
        new Vue({
            el: "#app",

            data() {
                return {
                    options: [{value: 'CA', label: 'Canada'}],
                    notifications: <?php echo json_encode($module->getNotifications()) ?>,
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
                        // {
                        //     key: 'type',
                        //     sortable: true
                        // },
                        {
                            key: 'status',
                            sortable: true
                        },
                        {
                            key: 'created_at',
                            sortable: true
                        },
                        {
                            key: 'modified_at',
                            sortable: true
                        },
                        {
                            key: 'redcap_pid',
                            label: 'REDCap PID',
                            sortable: true
                        }
                    ],
                    filter: null,
                    isDisabled: false,
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
                    allEms: [],
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
                    base_portal_url: "<?php echo $module->getClient()->getPortalBaseURL() ?>",
                    project_status: "<?php echo $module->getProject()->project['status'] ?>",
                    portal_projects_list: <?php echo json_encode($module->getUser()->getProjectPortalList()) ?>,
                    ticket_types: <?php echo json_encode($module->getSupport()->getJiraIssueTypes()) ?>,
                    project_portal_id: "",
                    header: "<?php echo str_replace(array("\n", "\r", "\""), array("\\n", "\\r", ""), $module->getSystemSetting('rit-dashboard-main-header'));; ?>",
                    ajaxCreateJiraTicketURL: "<?php echo $module->getUrl('ajax/support/create_jira_ticket.php') ?>",
                    ajaxUserTicketURL: "<?php echo $module->getUrl('ajax/user/get_user_tickets.php', false, true) ?>",
                    ajaxProjectEMstURL: "<?php echo $module->getUrl('ajax/entity/get_project_external_modules.php', false, true) ?>",
                    ajaxGenerateSignedAuthURL: "<?php echo $module->getUrl('ajax/portal/generate_rma.php', false, true) ?>",
                    ajaxAppendSignedAuthURL: "<?php echo $module->getUrl('ajax/portal/append_to_existing_ema.php', false, true) ?>",
                    ajaxGetSignedAuthURL: "<?php echo $module->getUrl('ajax/portal/get_rma.php', false, true) ?>",
                    ajaxPortalProjectsListURL: "<?php echo $module->getUrl('ajax/user/get_user_r2p2_project_list.php', false, true) ?>",
                    attachREDCapURL: "<?php echo $module->getURL('ajax/portal/project_attach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    detachREDCapURL: "<?php echo $module->getURL('ajax/portal/project_detach.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    projectPortalSectionURL: "<?php echo $module->getURL('ajax/portal/project_setup.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    portal_linkage_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-portal-linkage-tab-header')); ?>",
                    tickets_header: '<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-ticket-tab-header')); ?>',
                    external_modules_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-external-modules-tab-header')); ?>",
                    hasManagePermission: "<?php echo $module->getUser()->isUserHasManagePermission(); ?>",
                    portalREDCapMaintenanceAgreement: [],
                    refCount: 0,
                    isLoading: true,
                    currentProjectTickets: 'Yes',
                    currentProjectEms: 'Yes',
                    openTickets: 'Yes',
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
                hasREDCapMaintenanceAgreement: function () {
                    if (this.linked() == true && this.hasManagePermission == true && this.portalREDCapMaintenanceAgreement.project_id == undefined) {
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
                            return n.current_pid === true;
                        });
                    } else {
                        this.items = this.allItems
                    }

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
                filterOpenTickets() {
                    if (this.openTickets === 'Yes') {
                        this.items = this.allItems.filter(function (n) {
                            return n.status !== 'Done';
                        });
                    } else {
                        this.filterTickets()
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
                    this.manupilateProjectInfo()
                },
                manupilateProjectInfo: function () {
                    var element = document.getElementById('subheaderDiv2')
                    element.style.float = "right"
                    element.className = ''
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
                                this.items_em = this.allEms = response.data.data;
                                this.totalRows_em = this.items_em.length;
                                for (var i = 0; i < this.items_em.length; i++) {
                                    this.totalFees += parseFloat(this.items_em[i].maintenance_fees)
                                }
                                this.filterEms()
                            }
                        }).then(() => {
                        // only try to get signed auth if project is linked
                        if (this.linked()) {
                            this.getSignedAuth()
                        }
                    }).then(() => {
                        if (this.linked() == false) {

                            // global alert message that is none dismissible
                            if (this.project_status == "0" && this.totalFees > 0) {
                                // Dev-mode redcap project
                                this.showNoneDismissibleAlert = true
                                this.noneDismissibleAlertMessage += this.notifications.get_project_ems_dev
                                this.noneDismissibleVariant = "warning"
                            } else if (this.project_status == "1" && this.totalFees > 0) {
                                // Production mode redcap project
                                this.showNoneDismissibleAlert = true
                                this.noneDismissibleAlertMessage += this.notifications.get_project_ems_prod
                                this.noneDismissibleVariant = "danger"
                            }
                        }
                        if (this.hasREDCapMaintenanceAgreement() === false) {
                            // project in dev mode but has EM with monthly fees
                            if (this.totalFees > 0 && this.project_status == "0") {
                                if (this.linked() === false) {
                                    this.setEMAlertMessage("warning", this.notifications.get_project_ems_dev_2, true)
                                }
                                // project in prod mode but has EM with monthly fees
                            }
                            if (this.totalFees > 0 && this.project_status == "1") {
                                this.setEMAlertMessage("danger", this.notifications.get_project_ems_prod_2, true)
                                // project in analysis mode but has EM with monthly fees
                            }
                            if (this.totalFees > 0 && this.project_status == "2") {
                                this.setEMAlertMessage("info", this.notifications.get_project_ems_analysis, true)
                            }
                        }
                    });
                },
                submitTicket: function () {
                    axios.post(this.ajaxCreateJiraTicketURL, this.ticket)
                        .then(response => {
                            this.getUserTickets()
                            this.$refs['generic-modal'].hide()
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.alertMessage = response.data.message
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                    ;
                },
                attachRedCapProject: function () {
                    var project = []

                    project = this.ticket.project_portal_id

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
                            this.portalREDCapMaintenanceAgreement = response.data;
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
                        portal_sow_id: this.portalREDCapMaintenanceAgreement.id,
                        external_modules: this.items_em
                    })
                        .then(response => {
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.showPortalLinkageDismissibleAlert = false
                            this.showEMDismissibleAlert = false
                            this.alertMessage = response.data.message
                            this.portalREDCapMaintenanceAgreement = response.data;
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                getSignedAuth: function () {
                    axios.get(this.ajaxGetSignedAuthURL)
                        .then(response => {
                            this.portalREDCapMaintenanceAgreement = response.data;
                            if (this.determineREDCapStep() === 1) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_1, true)
                            } else if (this.determineREDCapStep() === 2) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_2, true)

                            } else if (this.determineREDCapStep() === 3) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_3, true)
                            }
                        });
                },
                getREDCapProjectsNames: function () {
                    var projects = ''

                    for (var i = 0; i < this.portalREDCapMaintenanceAgreement.redcap.length; i++) {
                        projects += this.portalREDCapMaintenanceAgreement.redcap[i]['redcap_project_name'] + ','

                    }
                    return projects.substring(0, projects.length - 1);
                },
                openWindow: function (url) {
                    window.open(url, '_blank')
                },
                determineREDCapStep: function () {
                    if (this.hasREDCapMaintenanceAgreement() === false) {
                        return 1
                    } else {
                        if (this.portalREDCapMaintenanceAgreement.redcap !== undefined) {
                            return 2
                        }
                        if (this.portalREDCapMaintenanceAgreement.sow_status !== 2) {
                            return 3
                        }
                        if (this.portalREDCapMaintenanceAgreement.sow_status === 2) {
                            return 4
                        }
                        return 5
                    }
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