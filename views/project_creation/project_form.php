<b-container fluid class="mt-3">
    <strong>{{notifications.project_creation_project_form_title}}</strong>
    <p v-html="notifications.project_creation_project_form_body"></p>

    <hr>
    <b-card-text>
        <b-alert
                :variant="variant"
                dismissible
                fade
                :show="showDismissibleAlert">
            <b class="row" v-html="alertMessage"></b>
        </b-alert>
    </b-card-text>
    <b-form-group
            label="Project Name"
            label-for="project_name"
            description="A short name or phrase that describes the project"
            class="mb-3"
    >
        <b-input-group class="mb-2">
            <b-input-group-prepend is-text>
                <b-icon icon="folder"></b-icon>
            </b-input-group-prepend>
            <b-form-input id="project_name" v-model="ticket.project_portal_name" type="text"
                          placeholder="e.g. SnapDX Centrifugal handifuge"></b-form-input>
        </b-input-group>
    </b-form-group>


    <b-form-group
            label="Project Description"
            label-for="project_description"
            description="A short description of your project or study at a high level, similar to a journal abstract.  TDS management would like to understand what the overall objective is for the work that you and your team are doing. "
            class="mb-3"
    >
        <b-input-group class="mb-2">
            <b-form-textarea id="ticket.project_portal_description" v-model="ticket.project_portal_description"
                             type="text"
                             placeholder="e.g. SnapDX Centrifugal handifuge"></b-form-textarea>
        </b-input-group>
    </b-form-group>

    <b-form-group label="Project Type" v-slot="{ ariaDescribedby }">
        <b-form-radio v-model="ticket.project_portal_type" :aria-describedby="ariaDescribedby" name="project_type"
                      value="1">Research Project
        </b-form-radio>
        <b-form-radio v-model="ticket.project_portal_type" :aria-describedby="ariaDescribedby" name="project_type"
                      value="2">Quality Improvement
        </b-form-radio>
        <b-form-radio v-model="ticket.project_portal_type" :aria-describedby="ariaDescribedby" name="project_type"
                      value="3">Other
        </b-form-radio>
    </b-form-group>

    <div v-if="Object.keys(irb).length !== 0">
        <h5>Following IRB will be linked to your project: {{irb.protocol_title}}</h5>
    </div>
</b-container>