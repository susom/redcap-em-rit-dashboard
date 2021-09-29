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
        <span v-html="portal_linkage_header"></span>
    </b-row>
    <div v-if="linked() == false">
        <b-card sub-title="R2P2 (RIT Research Project Portal)" class="mt-3">
            <b-row class="mt-3">
                Your REDCap project has not been linked to a R2P2 (RIT Research Project Portal). Please register/link
                this
                project
                below:
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
            <b-row class="mt-3">
                <strong>Q: What is the Research IT Portal?</strong><br></b-row>
            <b-row class="mt-3">
                <p> The Research IT Portal acts as a central hub to organize support, information, and finance details
                    for
                    research projects. Please check out the portal at https://rit-portal.med.stanford.edu
                </p></b-row>
            <b-row class="mt-3"><strong>Q: Why do I want to link mt REDCap project to the Portal?</strong><br></b-row>
            <b-row class="mt-3"><p>Linking your REDCap project to the portal has many benefits:</p></b-row>
            <b-row class="mt-3">
                <ul>
                    <li>Your support inquiries will be visible on the portal and can be shared with other team
                        members.
                    </li>
                    <li>Professional services and maintenance contracts can be viewed and approved</li>
                    <li>Consultations for project assistance can be requested and tracked</li>
                </ul>

            </b-row>
        </b-card>
    </div>
    <div v-else>
        <b-card sub-title="R2P2 (RIT Research Project Portal)" class="mt-3">
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