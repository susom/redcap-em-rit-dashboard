<?php

namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */


try {
    $monthlyFees = $module->getEntity()->getTotalMonthlyPayment($module->getProjectId());
    $module->setState($module->getProject()->project['status'] == '1', $monthlyFees, isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']), $module->getPortal()->getHasRMA(), $module->getPortal()->getRMAStatus());


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
                <b class="row" v-html="alertMessage"></b>
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
                    <b-tabs content-class="" :value="activeTabIndex">
                        <b-tab title="R2P2">
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
                        <!--                        <b-tab title="Invoice Line Items">-->
                        <!--                            --><?php
                        //                            require("tabs/line_items.php");
                        //                            ?>
                        <!--                        </b-tab>-->
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
        var ajaxCalls = [];
        Vue.component('v-select', VueSelect.VueSelect);
        // Vue.component('Navigation', Navigation)
        new Vue({
            el: "#app",
            computed: {
                activeTabIndex() {
                    const route = this.fullURL.toLowerCase();
                    var parts = route.split("#")
                    const im = this.tabsPathsDictionary.findIndex(element => element === parts[1]) || 0;
                    return im;
                }
            },
            data() {
                return {
                    PROD: 1,
                    HAS_FEES: 2,
                    LINKED: 4,
                    HAS_RMA: 8,
                    APPROVED_RMA: 16,
                    projectState: <?php echo $module->getState() ?>,
                    fullURL: window.location.href,
                    tabsPathsDictionary: ['r2p2', 'support', 'external-modules', 'payment-history'],
                    options: [{value: 'CA', label: 'Canada'}],
                    notifications: <?php echo json_encode($module->getNotifications()) ?>,
                    variant: "danger",
                    noneDismissibleVariant: "danger",
                    portalLinkageVariant: "danger",
                    EMVariant: "danger",
                    line_items_variant: "danger",
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
                    perPage: 100,
                    items: [],
                    allItems: [],
                    alertMessage: '',
                    portalLinkageAlertMessage: '',
                    EMAlertMessage: '',
                    line_items_alert_message: '',
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
                    fields_line_items: [
                        {
                            key: 'id',
                            label: 'ID',
                            sortable: true
                        },
                        {
                            key: 'sow_title',
                            label: 'RMA Title',
                            sortable: true
                        },
                        {
                            key: 'monthly_payment',
                            label: 'Monthly Payment',
                            sortable: true
                        },
                        {
                            key: 'number_of_months',
                            label: 'Number of Months',
                            sortable: true
                        },
                        {
                            key: 'total_amount',
                            label: 'Total Amount',
                            sortable: true
                        },
                        {
                            key: 'status',
                            label: 'Payment Status',
                            sortable: true
                        }, {
                            key: 'line_item_month_year',
                            label: 'Payment Date',
                            sortable: true
                        },
                        {
                            key: 'is_recurring',
                            label: 'Is recurring?',
                            sortable: true
                        }
                    ],
                    filter_em: null,
                    filter_line_items: null,
                    currentPage_em: 1,
                    current_page_line_items: 1,
                    totalRows_em: 0,
                    total_rows_line_items: 0,
                    perPage_em: 100,
                    per_page_line_items: 100,
                    items_em: [],
                    items_line_items: [],
                    all_items_line_items: [],
                    allEms: [],
                    totalFees: 0,
                    showDismissibleAlert: false,
                    showNoneDismissibleAlert: false,
                    showPortalLinkageDismissibleAlert: false,
                    showEMDismissibleAlert: false,
                    show_line_items_dismissibleAlert: false,
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
                    projectPortalRMALineItems: "<?php echo $module->getURL('ajax/portal/get_line_items.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    ajaxUpdateProjectEMUtil: "<?php echo $module->getURL('ajax/manager/update_project_em_util.php', false, true) . '&pid=' . $module->getProjectId() ?>",
                    portal_linkage_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-portal-linkage-tab-header')); ?>",
                    tickets_header: '<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-ticket-tab-header')); ?>',
                    external_modules_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-external-modules-tab-header')); ?>",
                    line_items_header: "<?php echo str_replace(array("\n", "\r"), array("\\n", "\\r"), $module->getSystemSetting('rit-dashboard-line-items-tab-header')); ?>",
                    hasManagePermission: "<?php echo $module->getUser()->isUserHasManagePermission(); ?>",
                    portalREDCapMaintenanceAgreement: [],
                    refCount: 0,
                    isLoading: true,
                    currentProjectTickets: 'Yes',
                    currentProjectEms: 'Yes',
                    current_project_line_items: 'Yes',
                    openTickets: 'Yes',
                    emptyTicketsTable: "No Tickets Found",
                    bodyMessage: '',
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
                    //if (this.linked() == true && this.hasManagePermission == true && this.portalREDCapMaintenanceAgreement.project_id == undefined) {
                    if (this.portalREDCapMaintenanceAgreement.project_id == undefined) {
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
                            return n.maintenance_fees > 0 || n.maintenance_monthly_cost != 'Module Disabled';
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
                onFilteredLineItems(filteredItems) {
                    // Trigger pagination to update the number of buttons/pages due to filtering
                    this.totalRows_em = filteredItems.length
                    this.currentPage_em = 1
                },
                prepareComponent: function () {
                    this.getUserTickets()
                    this.getProjectEMs()
                    this.manupilateProjectInfo();
                    this.processURL()
                },
                processURL: function () {
                    const urlSearchParams = new URLSearchParams(window.location.search);
                    const params = Object.fromEntries(urlSearchParams.entries());
                    if (params['open-support-modal'] != undefined && params['open-support-modal'] == 'true') {
                        this.$refs['generic-modal'].show()
                    }
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

                                this.calculateTotalFees()
                                this.filterEms()
                            }
                        }).then(() => {
                        // only try to get signed auth if project is linked
                        if (this.linked()) {
                            return this.getSignedAuth()
                        } else {
                            if (this.projectState & this.HAS_RMA === false) {
                                this.emTabAlerts()
                            }
                            var notification = this.notifications['project_state_' + this.projectState]
                            notification = this.replaceNotificationsVariables(notification, {
                                'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333',
                                'a': '#'
                            })
                            this.setEMAlertMessage(this.determineProjectAlert(), notification, true)
                            this.showNoneDismissibleAlert = true
                            this.noneDismissibleAlertMessage += notification
                            this.noneDismissibleVariant = this.determineProjectAlert()
                            // global alert message that is none dismissible
                            // if ((this.projectState & this.PROD) === false && this.projectState & this.HAS_FEES) {
                            //     // Dev-mode redcap project
                            //     this.showNoneDismissibleAlert = true
                            //     this.noneDismissibleAlertMessage += notification
                            //     this.noneDismissibleVariant = this.determineProjectAlert()
                            // } else if (this.projectState & this.PROD && this.projectState & this.HAS_FEES) {
                            //     // Production mode redcap project
                            //     this.showNoneDismissibleAlert = true
                            //     this.noneDismissibleAlertMessage += this.replaceNotificationsVariables(this.notifications.get_project_ems_prod, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})
                            //     this.noneDismissibleVariant = "danger"
                            // } else if (this.projectState & this.PROD && (this.projectState & this.HAS_FEES) === false) {
                            //     // Production mode redcap project
                            //     this.showNoneDismissibleAlert = true
                            //     this.noneDismissibleAlertMessage += this.replaceNotificationsVariables(this.notifications.get_project_ems_prod_no_fees, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})
                            //     this.noneDismissibleVariant = "warning"
                            // }
                        }
                    });
                },
                replaceNotificationsVariables: function (notification, variables) {
                    for (var key in variables) {
                        notification = notification.replace("[" + key + "]", variables[key])
                    }
                    return notification
                },
                submitTicket: function () {
                    axios.post(this.ajaxCreateJiraTicketURL, this.ticket)
                        .then(response => {
                            this.getUserTickets()
                            this.$refs['generic-modal'].hide()
                            this.$refs['ticket-modal'].show()
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.alertMessage = response.data.message
                            this.bodyMessage = response.data.message
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                    ;
                },
                updateExternalModuleList: function () {
                    axios.post(this.ajaxUpdateProjectEMUtil)
                        .then(response => {
                            this.getProjectEMs()
                            this.variant = 'success'
                            this.showDismissibleAlert = true
                            this.alertMessage = response.data.message
                            this.bodyMessage = response.data.message
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
                calculateTotalFees: function () {
                    this.totalFees = 0
                    for (var i = 0; i < this.items_em.length; i++) {
                        this.totalFees += parseFloat(this.items_em[i].maintenance_fees)
                    }
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
                            this.setEMAlertMessage("success", response.data.message, true)

                            //this will update em list in case the list changed during RMA genertion.
                            this.items_em = response.data.ems
                            this.calculateTotalFees()
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
                            this.setEMAlertMessage("success", response.data.message, true)
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                getSignedAuth: function () {
                    axios.get(this.ajaxGetSignedAuthURL + '&monthly_payment=' + this.totalFees)
                        .then(response => {
                            this.portalREDCapMaintenanceAgreement = response.data;
                            if (this.determineREDCapStep() === 1) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_1, true)
                            } else if (this.determineREDCapStep() === 2) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_2, true)

                            } else if (this.determineREDCapStep() === 3) {
                                this.setPortalLinkageAlertMessage("warning", this.notifications.get_rma_step_3, true)
                            }
                            if (this.hasREDCapMaintenanceAgreement() === false) {
                                this.emTabAlerts()
                            }
                        }).then(response => {
                        // when RMA is loaded then load line items for it.
                        //this.getRMALineItems()
                    }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                getRMALineItems: function () {
                    axios.get(this.projectPortalRMALineItems + '&rma_id=' + this.portalREDCapMaintenanceAgreement.id)
                        .then(response => {
                            if (response.data.data !== undefined) {
                                this.processRMALineItems(response.data.data);
                            }
                        }).catch(err => {
                        this.variant = 'danger'
                        this.showDismissibleAlert = true
                        this.alertMessage = err.response.data.message
                    });
                },
                processRMALineItems: function (data) {
                    var arr = []
                    for (var i = 0; i < data.length; i++) {
                        // has invoice line items
                        if (data[i]['invoice_line_items'].length > 0) {
                            for (var j = 0; j < data[i]['invoice_line_items'].length; j++) {
                                arr.push({
                                    'id': data[i]['invoice_line_items'][j]['id'],
                                    'sow_title': data[i]['sow_title'],
                                    'monthly_payment': data[i]['monthly_payment'],
                                    'number_of_months': data[i]['number_of_months'],
                                    'total_amount': data[i]['total_amount'],
                                    'status': data[i]['invoice_line_items'][j]['status'] === 0 ? 'Not Processed' : 'Processed',
                                    'line_item_month_year': data[i]['invoice_line_items'][j]['line_item_month_year'],
                                    'is_recurring': data[i]['is_recurring'] === true ? 'Yes' : 'No',
                                })
                            }
                        }
                    }
                    this.items_line_items = this.all_items_line_items = arr;
                    this.total_rows_line_items = this.all_items_line_items.length;
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
                emTabAlerts: function () {
                    var notification = this.notifications['project_state_' + this.projectState]
                    notification = this.replaceNotificationsVariables(notification, {
                        'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333',
                        'a': '#r2p2'
                    })
                    this.setEMAlertMessage(this.determineProjectAlert(), notification, true)
                    // // project in dev mode but has EM with monthly fees
                    // if ((this.projectState & this.PROD) === false && this.projectState & this.HAS_FEES) {
                    //     this.setEMAlertMessage("warning", this.notifications.get_project_ems_dev_2, true)
                    //     // project in prod mode but has EM with monthly fees
                    // }
                    // if (this.projectState & this.HAS_FEES && this.projectState & this.PROD) {
                    //     var notification = this.replaceNotificationsVariables(this.notifications.get_project_ems_prod_2, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})
                    //     this.setEMAlertMessage("danger", notification, true)
                    //     // project in analysis mode but has EM with monthly fees
                    // }
                    // if ((this.projectState & this.HAS_FEES) === false && this.projectState & this.PROD) {
                    //     var notification = this.replaceNotificationsVariables(this.notifications.get_project_ems_prod_no_fees_2, {'wiki': 'https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333'})
                    //     this.setEMAlertMessage("warning", notification, true)
                    //     // project in analysis mode but has EM with monthly fees
                    // }
                    if (this.projectState & this.HAS_FEES && (this.projectState & this.PROD) === false && this.project_status === "2") {
                        this.setEMAlertMessage("info", this.notifications.get_project_ems_analysis, true)
                    }
                },
                determineProjectAlert: function () {
                    var alert = 'warning'

                    if (this.projectState & this.PROD === false && this.projectState & this.HAS_FEES === false) {
                        return 'success'
                    } else if (this.projectState & this.PROD === false && this.projectState & this.HAS_FEES) {
                        if (this.projectState & this.LINKED && this.projectState & this.HAS_RMA && this.projectState & this.APPROVED_RMA) {
                            return 'success'
                        } else {
                            return 'warning'
                        }
                    }
                    if (this.projectState & this.PROD && this.projectState & this.HAS_FEES === false) {
                        return 'success'
                    } else if (this.projectState & this.PROD && this.projectState & this.HAS_FEES) {
                        if (this.projectState & this.LINKED && this.projectState & this.HAS_RMA && this.projectState & this.APPROVED_RMA) {
                            return 'success'
                        } else {
                            return 'danger'
                        }
                    }
                    return alert
                },
                /**
                 * below is different from project state. this step for RMA
                 * @returns {number}
                 */
                determineREDCapStep: function () {
                    if (this.projectState & this.HAS_RMA === false) {
                        return 1
                    } else {
                        /**
                         APPROVED_PENDING_DEVELOPMENT = 2
                         APPROVED_ACTIVE_DEVELOPMENT = 6
                         APPROVED_MAINTENANCE = 7
                         * @type {number[]}
                         */
                        var statsus = [2, 6, 7]
                        if (this.portalREDCapMaintenanceAgreement.redcap !== undefined) {
                            return 2
                        }
                        if (!statsus.includes(this.portalREDCapMaintenanceAgreement.sow_status)) {
                            return 3
                        }
                        if (statsus.includes(this.portalREDCapMaintenanceAgreement.sow_status)) {
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