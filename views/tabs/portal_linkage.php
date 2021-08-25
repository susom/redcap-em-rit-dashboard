<div class="row">
    <span v-html="portal_linkage_header"></span>
</div>
<div class="row">
    <div class="row">
        <div class="col-3">
            <div v-if="linked() == true" id="lbl-" style="color:green;"> Linked</div>
            <div v-else id="lbl-" style="color:#F47F6C;">Not Linked</div>
        </div>
        <div class="col-9">
            <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                           value-field="id"
                           text-field="project_name">
            </b-form-select>
        </div>

    </div>
    <div class="row">
        <div class="offset-3 mt-2">
            <b-button v-if="linked() == false" @click="attachRedCapProject()" variant="success">Attache Selected
                Project
            </b-button>
            <b-button v-else @click="detachRedCapProject()" variant="danger">Detach Selected Project</b-button>
        </div>
    </div>
</div>