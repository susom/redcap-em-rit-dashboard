<b-container fluid class="mt-3">
    <b-row class="mt-2">
        <b-alert :variant="portalLinkageVariant"
                 dismissible
                 fade
                 :show="showPortalLinkageDismissibleAlert"
        ><i class="fas fa-exclamation-circle"></i>
            {{portalLinkageAlertMessage}}
        </b-alert>
    </b-row>
    <b-row class="mt-2">
        <b-col><span v-html="portal_linkage_header"></span></b-col>
    </b-row>
    <b-row>
        <b-col>
            <h6>
                R2P2 (<u>R</u>esearch IT <u>R</u>esearch <u>P</u>roject <u>P</u>ortal) is a web platform that
                coordinates
                applications, services, and support for researchers working with the Research IT team and Stanford
                Technology and Digital Solutions (TDS).
            </h6>
        </b-col>
    </b-row>
    <div v-if="linked() == false">
        <b-card sub-title="R2P2 - REDCap Linkage" class="mt-3">
            <b-alert v-if="totalFees > 0" variant="danger"
                     fade
                     show
            >
                <b-row>

                    <b-col class="justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                                    class="fas fa-exclamation-circle"></i></h5>
                        {{notifications.r2p2_tab_rma_card_not_linked_danger_message}}<br>

                    </b-col>

                </b-row>
            </b-alert>
            <b-alert v-if="totalFees == 0" variant="warning"
                     fade
                     show
            >
                <b-row>

                    <b-col class="justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                                    class="fas fa-exclamation-circle"></i></h5>
                        {{notifications.r2p2_tab_rma_card_no_fees_warning_message}}
                    </b-col>

                </b-row>
            </b-alert>




            <b-row class="text-center">
                <b-col>
                    <b-button size="sm" class="mt-3" variant="success" v-b-modal.project-creation-modal>Find/Create
                        R2P2
                        Project
                    </b-button>
                </b-col>
            </b-row>

        </b-card>

        <b-row class="mt-3">
            <b-col>
                For more information please visit <a target="_blank" href="https://medwiki.stanford.edu/x/uiK3Cg">the
                    R2P2 Wiki</a>
            </b-col>
        </b-row>
    </div>
    <div v-else>
        <b-alert variant="success"
                 fade
                 show
        >
            <b-row>

                <b-col class="justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                                class="fas fa-check-circle"></i></h5>
                    {{notifications.r2p2_tab_rma_card_linked_success_message}}
                    <a :href="ticket.project_portal_url" target="_blank" class="ml-1"><i
                                class="fas fa-external-link-alt"></i>
                            <span>{{ticket.project_portal_name}}</span></a>
                    </b-col>

                </b-row>
            </b-alert>


            <b-row class="mt-3">
                <b-col lg="12">
                    If this is incorrect, please open a support ticket with additional detail for assistance.
                </b-col>
            </b-row>
    </div>
</b-container>