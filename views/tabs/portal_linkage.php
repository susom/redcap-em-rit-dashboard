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
            R2P2 (<u>R</u>esearch IT <u>R</u>esearch <u>P</u>roject <u>P</u>ortal) is a web platform that coordinates
            applications, services, and support for researchers working with the Research IT team and Stanford
            Technology and
            Digital Solutions (TDS).
        </b-col>
    </b-row>
    <div v-if="linked() == false">
        <b-card sub-title="This REDCap Project has not been linked to R2P2" class="mt-3">
            <b-row class="mt-3">
                <b-col>
                    <p>Please register your research project in R2P2 and then link this REDCap project to the R2P2
                        entry.</p>
                    <p>If you are already part of the R2P2 project, you can link this REDCap project using the dropdown
                        below:</p>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col lg="7" class="mt-3">
                    <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                                   value-field="id"
                                   text-field="project_name">
                    </b-form-select>
                </b-col>
                <b-col lg="5" class="justify-content-center align-self-center">
                    <b-button size="sm" @click="attachRedCapProject()" variant="success">Attach Selected
                        Project
                    </b-button>
                </b-col>
            </b-row>
            <b-row>
                <b-col>-- OR --</b-col>
            </b-row>
            <b-row>
                <b-col>
                    If you do not see your project in the list, click here to search for or create a new entry in R2P2
                    for this research project
                    <b-button size="sm" variant="success" href="https://rit-portal.med.stanford.edu/"><span
                                class="btn-xs btn-success">Find or Create a R2P2 Project</span></b-button>
                </b-col>
            </b-row>
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
                    <ul>
                        <li></li>
                    </ul>
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
        </b-card>
    </div>
    <div v-else>
        <b-card sub-title="R2P2 (ResearchIT Research Project Portal)" class="mt-3">
            <b-row class="mt-3">
                <b-col lg="12">
                    This REDCap project is part of <a :href="ticket.project_portal_url" target="_blank"><i
                                class="fas fa-external-link-alt"></i> <span>{{ticket.project_portal_name}}</span></a>
                </b-col>
            </b-row>

            <b-row class="mt-3">
                <b-col lg="12">
                    If this is incorrect, please open a support ticket with additional details.
                </b-col>
            </b-row>
        </b-card>
        <b-card sub-title="REDCap Maintenance Agreement" class="mt-3">
            <b-row v-if="hasSignedAuthorization() == false" class="mt-2">
                TODO Add about REMA paragraph.
                <b-col md="6">
                    <b-button size="sm" variant="success"

                              @click="generateSignedAuth()">
                        Generate REDCap Maintenance Agreement
                    </b-button>
                </b-col>
            </b-row>
            <b-row v-if="portalSignedAuth.redcap != undefined" class="mt-2">
                <b-col md="6">
                    <b-button size="sm" variant="success"

                              @click="appendSignedAuth()">
                        Authorize this REDCap Project to use an Approved REDCap Maintenance Agreement
                    </b-button>
                </b-col>
            </b-row>
            <b-row v-else-if="portalSignedAuth.project_id != undefined && portalSignedAuth.redcap == undefined"
                   class="mt-2">
                <b-col md="12">
                    This REDCap project has an active REDCap External Module Maintenance Agreement. Please see the
                    External
                    Modules tab for details.
                </b-col>
            </b-row>
        </b-card>
    </div>
</b-container>