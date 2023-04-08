<b-container fluid class="mt-3">
    <b-row v-if="determineREDCapStep() < 4" class="mt-2">
        <!-- R2P2 Project does not have a REDCap Maintenance Agreement SoW Created Yet

            hasREDCapMaintenanceAgreement() means R2P2 project has SoW
            portalREDCapMaintenanceAgreement means SoW exists
            portalREDCapMaintenanceAgreement.redcap != undefined" means this redcap is not associated to SoW

            [ R2P2 does not have SoW ] => Step 1: Create one
            [ R2P2 has SoW but redcap not linked ] => Step 2: Link RMA to this REDCap
            [ R2P2 has SoW and redcap linked, but SoW not approved ] => Step 3: R2P2 Project Admin must approve RMA

            [ R2P2 has approved,linked RMA ] => !SUCCESS! checkbox with nice message.

        -->
        <b-col>
            <p>
                We recommend all REDCap projects be bound to an approved REDCap Maintenance Agreement. Some
                projects (e.g. those with external modules that have fees or other custom code) will require an
                approved REDCap Maintenance Agreement prior to being moved to Production mode, so it is best to
                complete these steps before going live to avoid delays.
            </p>
            <p>
                <i>Additional details on the maintenance agreement can be found
                    at the <a href="https://medwiki.stanford.edu/x/dZeWCg" target="_blank" class="ml-1"><i
                            class="fas fa-external-link-alt"></i> REDCap Wiki </a>
                </i>
            </p>

            <b-alert class="d-flex d-inline-block"
                     variant="danger"
                     fade
                     show
            >
                <b-row>
                    <b-col class="justify-content-center align-self-center" lg="12"><h5
                            class="d-inline-block  p-1"><i
                                class="fas fa-exclamation-circle"></i></h5>
                        {{notifications.r2p2_tab_rma_card_danger_message}}
                    </b-col>
                </b-row>
            </b-alert>
        </b-col>
    </b-row>


    <!-- STEP 1 -->
    <div v-if="determineREDCapStep() == 1">
        <b-row>
            <b-col md="6">
                <b-button size="sm" variant="success"
                          @click="generateSignedAuth()">
                    <!--                            <span class="fa-stack fa-1x">-->
                    <!--                              <i class="text-danger fa fa-circle fa-stack-2x"></i>-->
                    <!--                              <strong class="fa-stack-1x calendar-text fa-inverse">1</strong>-->
                    <!--                            </span>-->
                    Step 1: Generate a REDCap Maintenance Agreement
                </b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <p>
                    After the REDCap Maintenance agreement has been created, a R2P2 project admin or finance
                    user will need to approve the agreement and provide a PTA on the R2P2 portal.
                </p>
            </b-col>
        </b-row>
    </div>
    <div v-else-if="determineREDCapStep() > 1">
        <b-row>
            <b-col>

                <div class="text-success justify-content-start align-self-center">
                    <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
                    Step 1: REDCap Maintenance Agreement Created
                </div>
            </b-col>
        </b-row>
    </div>


    <!-- STEP 2 -->
    <div v-if="determineREDCapStep() == 2">
        <b-row class="mt-2">
            <b-col md="6">
                <b-button size="sm" variant="success"
                          @click="appendSignedAuth()">
                    Step 2: Add this REDCap Project to the R2P2 REDCap Maintenance Agreement
                </b-button>
                <p>
                    The linked R2P2 project already has a maintenance agreement in place but this particular
                    REDCap
                    project is not associated to it. Click below to add this REDCap project to the existing
                    maintenance agreement.
                </p>
            </b-col>
        </b-row>
    </div>
    <div v-else-if="determineREDCapStep() > 2">
        <b-row>
            <b-col>

                <div class="text-success justify-content-start align-self-center">
                    <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
                    Step 2: REDCap project linked to R2P2 REDCap Maintenance Agreement
                </div>
            </b-col>
        </b-row>
    </div>


    <!-- STEP 3 -->
    <div v-if="determineREDCapStep() == 3"
         class="">
        <b-row class="">
            <b-col md="12">
                <div class="text-danger justify-content-start align-self-center">
                    <h5 class="d-inline-block  p-1"><i class="far fa-circle"></i></h5>
                    Step 3: The linked R2P2 REDCap Maintenance Agreement is awaiting approval.
                </div>
                <b-row>
                    <b-col>
                        <b-button :disabled="!isUserHasPermission([2,3])" size="sm" variant="primary" class="pl-4"
                                  @click="openModal('approve-sow-modal')">
                            Approve RMA
                        </b-button>
                        <!--                        OR-->
                        <!--                        <b-button size="sm" variant="secondary"-->
                        <!--                                  @click="openModal('sow-approval-instructions')">-->
                        <!--                            Email Approval Instruction-->
                        <!--                        </b-button>-->
                    </b-col>
                </b-row>
                <div>
                    <i>
                        Approval requires R2P2 admin or PI role
                    </i>
                </div>
            </b-col>
        </b-row>
    </div>
    <div v-else-if="determineREDCapStep() > 3">
        <b-row>
            <b-col>
                <div class="text-success justify-content-start align-self-center">
                    <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
                    Step 3: REDCap Maintenance Agreement Approved.
                </div>
            </b-col>
        </b-row>
    </div>


    <!-- STEP 4: SUCCESS -->
    <div v-if="determineREDCapStep() == 4">
        <b-row>
            <b-col>
                <p>
                    Additional details on the REDCap Maintenance Agreement can be found
                    at the <a href="https://medwiki.stanford.edu/x/dZeWCg" target="_blank" class="ml-1"><i
                            class="fas fa-external-link-alt"></i> REDCap Wiki </a>
                </p>
                <b-alert class="d-flex d-inline-block"
                         variant="success"
                         fade
                         show
                >
                    <b-row>
                        <b-col class="justify-content-center align-self-center" lg="12"><h5
                                class="d-inline-block  p-1"><i
                                    class="fas fa-check-circle"></i></h5> This REDCap Project is linked to
                            an
                            approved
                            REDCap Maintenance Agreement.
                        </b-col>
                    </b-row>
                </b-alert>
            </b-col>
        </b-row>
    </div>
</b-container>