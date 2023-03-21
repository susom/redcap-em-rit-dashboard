<b-container fluid class="mt-3">
    <b-row class="mt-3 text-center">
        <b-col>
            <h6>To link this REDCap project to R2P2, select from one of your existing R2P2 projects or Select **Create New Project**:</h6>
        </b-col>
    </b-row>
    <b-row class="mt-3">
        <b-col>

            <div class="d-flex justify-content-center center-list pl-3 pr-3">
                <b-input-group class="mt-3">
                    <v-select class="col-8 nopadding" v-model="ticket.project_portal_id"
                              :options="portal_projects_list"
                              value="id"
                              label="project_name" @input="selectProject">
                    </v-select>
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
            <b-button size="sm" variant="success" @click="openWindow('https://r2p2.med.stanford.edu/')">
                Go to R2P2 Website
            </b-button>
        </b-col>
    </b-row>
</b-container>