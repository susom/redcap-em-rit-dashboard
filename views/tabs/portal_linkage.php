<b-container>
    <b-row>
        <b-alert :variant="emVariant"
                 dismissible
                 fade
                 :show="showEMDismissibleAlert"
        >
            {{EMAlertMessage}}
        </b-alert>
    </b-row>
    <b-row>
        <span v-html="portal_linkage_header"></span>
    </b-row>
    <div v-if="linked() == false">
        <b-row class="my-1">
            Your REDCap project has not been linked to a Research IT Portal Project. Please register/link this project
            below:
        </b-row>
        <b-row class="my-1">
            <b-col lg="7" class="my-1">
                <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                               value-field="id"
                               text-field="project_name">
                </b-form-select>
            </b-col>
            <b-col lg="5">
                <b-button @click="attachRedCapProject()" variant="success">Attache Selected
                    Project
                </b-button>
            </b-col>
        </b-row>
        <b-row class="my-1">
            <strong>Q: What is the Research IT Portal?</strong><br></b-row>
        <b-row class="my-1">
            <p> The Research IT Portal acts as a central hub to organize support, information, and finance details for
                research projects. Please check out the portal at https://rit-portal.med.stanford.edu
            </p></b-row>
        <b-row class="my-1"><strong>Q: Why do I want to link my REDCap project to the Portal?</strong><br></b-row>
        <b-row class="my-1"><p>Linking your REDCap project to the portal has many benefits:</p></b-row>
        <b-row class="my-1">
            <ul>
                <li>Your support inquiries will be visible on the portal and can be shared with other team members.</li>
                <li>Professional services and maintenance contracts can be viewed and approved</li>
                <li>Consultations for project assistance can be requested and tracked</li>
            </ul>

        </b-row>
    </div>
    <div v-else>
        <b-row class="my-1">
            <b-col lg="12">
                This REDCap project is part of <strong>{{ticket.project_portal_name}}</strong>: You can view this
                research project’s
                details on the
                <b-button :href="ticket.project_portal_url" target="_blank" variant="success">
                    Research IT Portal {{ticket.project_portal_name}}
                </b-button>
            </b-col>
        </b-row>
        <!--        <b-row class="my-1">-->
        <!--            <b-col lg="12">-->
        <!--                <h5>{{ticket.project_portal_name}}</h5>-->
        <!--            </b-col>-->
        <!--        </b-row>-->
        <b-row class="my-1">
            <b-col lg="12">
                If this is incorrect, please open a support ticket with additional details.
            </b-col>
        </b-row>
        <b-row v-if="hasSignedAuthorization() == false" class="mt-2">
            <b-col md="6">
                <b-button variant="success"

                          @click="generateSignedAuth()">
                    Generate Signed Authorization for above EM
                </b-button>
            </b-col>
        </b-row>
        <b-row v-if="portalSignedAuth.redcap != undefined" class="mt-2">
            <b-col md="6">
                <b-button variant="success"

                          @click="appendSignedAuth()">
                    Authorize this REDCap Project to user Approved Maintenance Agreement
                </b-button>
            </b-col>
        </b-row>
        <b-row v-else-if="portalSignedAuth.project_id != undefined && portalSignedAuth.redcap == undefined"
               class="mt-2">
            <b-col md="12">
                This REDCap project has an active REDCap External Module Maintenance Agreement. Please see the External
                Modules tab for details.
            </b-col>
        </b-row>
    </div>
</b-container>