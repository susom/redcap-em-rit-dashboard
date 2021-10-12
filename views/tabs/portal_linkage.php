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
            <b-alert variant="danger"
                     fade
                     show
            >
                <b-row>

                    <b-col class="justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                                    class="fas fa-exclamation-circle"></i></h5> This REDCap project is NOT yet linked to
                        an R2P2 project.
                    </b-col>

                </b-row>
            </b-alert>
            <b-row class="mt-3">
                <b-col>
                    <h6>Please link this REDCap project to the correct R2P2 project.</h6>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <div class="d-flex justify-content-center">
                        <p>If you are already part of the R2P2 project, you can select the correct entry from the list
                            below:</p>
                    </div>
                    <div class="d-flex justify-content-center pl-3 pr-3">
                        <b-input-group class="mt-3">
                            <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list"
                                           value-field="id"
                                           text-field="project_name">
                            </b-form-select>
                            <b-input-group-append>
                                <b-button size="sm" @click="attachRedCapProject()" variant="success">Attach Selected
                                    Project
                                </b-button>
                            </b-input-group-append>

                        </b-input-group>
                    </div>
                </b-col>
            </b-row>

            <b-row class="mt-3 mb-3 text-center">
                <b-col>
                    <h6>-- OR --</h6>
                </b-col>
            </b-row>
            <b-row class="text-center">
                <b-col>
                    If you do not see the project in the list above, find/create the research project in R2P2:
                    <b-button size="sm" variant="success" @click="openWindow('https://rit-portal.med.stanford.edu/')">
                        Find or Create a R2P2 Project
                    </b-button>
                </b-col>
            </b-row>

        </b-card>
        <b-row class="mt-3">
            <b-col>
                <strong>Q: What is a R2P2 project and how is it different than a REDCap project?</strong><br>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <p> Each R2P2 Project represents the overall research project. Typically, it will be associated with
                    a protocol, an IRB number, a team of researchers, and one or more PTAs.
                    In some cases, there may be just one REDCap project for a R2P2 project while in many larger
                    research endeavors you end up with many different REDCap projects all
                    supporting the same R2P2 research effort.
                </p>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <strong>Q: Why don't I see my research project in the R2P2 project dropdown?</strong><br>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <p>
                    Only those R2P2 projects where you are a member will appear in the dropdown. If your research
                    project doesn't show up it could mean one of two things:
                <ul>
                    <li>The Research Project has not yet been registered on the R2P2 platform in which case you can
                        create it
                    </li>
                    <li>Or, the project has been created but you were not added as a member. In this case you can
                        find it on the R2P2 platform and request access
                    </li>
                </ul>
                In either case, your next step is to visit the R2P2 platform and begin the New Project Wizard which
                will help you find/create the entry.
                </p>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <strong>Q: Why do I want to link mt REDCap project to the Portal?</strong><br>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <p>Linking your REDCap project to the portal has many benefits:

                </p>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col>
                <ul>
                    <li>Your support inquiries will be visible on the portal and can be shared with other team
                        members.
                    </li>
                    <li>Professional services and maintenance contracts can be viewed and approved</li>
                    <li>Consultations for project assistance can be requested and tracked</li>
                </ul>
            </b-col>

        </b-row>
    </div>
    <div v-else>
        <b-card sub-title="R2P2 - REDCap Linkage" class="mt-3">
            <b-alert variant="success"
                     fade
                     show
            >
                <b-row>

                    <b-col class="justify-content-center align-self-center" lg="12"><h5 class="d-inline-block  p-1"><i
                                    class="fas fa-check-circle"></i></h5> This REDCap project is linked to R2P2 project
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
        </b-card>
        <b-card sub-title="R2P2 - REDCap Maintenance Agreement" class="mt-3">
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
                                            class="fas fa-exclamation-circle"></i></h5> This REDCap Project has not yet
                                been
                                linked to an approved REDCap Maintenance Agreement.
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

                        <div class = "text-success justify-content-start align-self-center">
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

                        <div class = "text-success justify-content-start align-self-center">
                            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
                            Step 2: REDCap Maintenance Agreement Created and linked

                        </div>
                    </b-col>
                </b-row>
            </div>


            <!-- STEP 3 -->
            <div v-if="determineREDCapStep() == 3"
                 class="mt-2">
                <b-row class="mt-2">
                    <b-col md="12">
                        Step 3: The linked R2P2 REDCap Maintenance Agreement is awaiting approval. Please have a project admin
                        or
                        finance user complete the agreement on the <a :href="portalREDCapMaintenanceAgreement.link" target="_blank"
                                                                      class="ml-1"><i
                                    class="fas fa-external-link-alt"></i> R2P2 portal </a>
                    </b-col>
                </b-row>
            </div>
            <div v-else-if="determineREDCapStep() > 3">
                <b-row>
                    <b-col>
                        <div class = "text-success justify-content-start align-self-center">
                            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>
                            Step 3: REDCap Maintenance Agreement Created and Linked and Approved.
                        </div>
                    </b-col>
                </b-row>
            </div>


            <!-- STEP 4: SUCCESS -->
            <div v-if="determineREDCapStep() == 4">
                <b-row >
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
                                                class="fas fa-check-circle"></i></h5> This REDCap Project is linked to an
                                    approved
                                    REDCap Maintenance Agreement.
                                </b-col>
                            </b-row>
                        </b-alert>
                    </b-col>
                </b-row>
            </div>
<!--            <div v-else-if="determineREDCapStep() > 4">-->
<!--                <b-row>-->
<!--                    <b-col>-->
<!---->
<!--                        <div class = "text-success justify-content-start align-self-center">-->
<!--                            <h5 class="d-inline-block  p-1"><i class="fa fa-check-circle "></i></h5>-->
<!--                            Step 4: REDCap Maintenance Agreement Created, Linked, and Approved-->
<!--                        </div>-->
<!--                    </b-col>-->
<!--                </b-row>-->
<!--            </div>-->


        </b-card>
    </div>
</b-container>