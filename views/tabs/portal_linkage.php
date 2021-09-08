<b-container>
    <b-row>
        <span v-html="portal_linkage_header"></span>
    </b-row>
    <div v-if="linked() == false">
        <b-row class="my-1">
            Your REDCap project has not been linked to a Research IT Portal Project. Please register/link this project
            below:
        </b-row>
        <b-row class="my-1">
            <b-col lg="4" class="my-1">
                <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                               value-field="id"
                               text-field="project_name">
                </b-form-select>
            </b-col>
            <b-col lg="3">
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
            <b-col lg="6">
                Your REDCap project has been linked to the following Research IT Portal Project:
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col lg="4">
                <h3>{{ticket.project_portal_name}}</h3>
            </b-col>
        </b-row>
        <b-row class="my-1">
            <b-col lg="4">
                If this is incorrect, please open a support ticket with additional details.
            </b-col>
        </b-row>
    </div>
</b-container>