<b-row>
    <span v-html="portal_linkage_header"></span>
</b-row>
<b-row>
    <b-col lg="3" class="my-1">
        <div v-if="linked() == true" id="lbl-" style="color:green;"> Linked</div>
        <div v-else id="lbl-" style="color:#F47F6C;">Not Linked</div>
    </b-col>
    <b-col lg="9" class="my-1">
        <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                       value-field="id"
                       text-field="project_name">
        </b-form-select>
    </b-col>
</b-row>
<b-row>
    <b-col offset="3">
        <b-button v-if="linked() == false" @click="attachRedCapProject()" variant="success">Attache Selected
            Project
        </b-button>
        <b-button v-else @click="detachRedCapProject()" variant="danger">Detach Selected Project</b-button>
    </b-col>
</b-row>
