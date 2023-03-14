<b-container fluid class="mt-3">
    <strong>{{notifications.project_creation_irb_form_title}}</strong>
    <p v-html="notifications.project_creation_irb_form_body"></p>
    <hr>
    <!-- Using slots -->
    <b-input-group class="mt-3">

        <b-form-input v-model="irb_num" placeholder="e.g. 54321"></b-form-input>
        <b-input-group-append>
            <b-input-group-text>
                <b-icon icon="search" aria-hidden="true" @click="searchIRB"/>

            </b-input-group-text>
        </b-input-group-append>
    </b-input-group>

    <hr>
    <b-card v-if="showIRBResultCard == true" title="Search Result">
        <b-card-text>
            <b-alert
                    :variant="variant"
                    dismissible
                    fade
                    :show="showDismissibleAlert">
                <b class="row" v-html="alertMessage"></b>
            </b-alert>
        </b-card-text>
        <b-card-text v-if="Object.keys(irb).length !== 0">
            <b-row>
                <b-col>
                    <h5>
                        <b-badge variant="secondary">
                            <b-icon icon="folder2" aria-hidden="true"/>
                            </b-icon> Protocol Title
                        </b-badge>
                        {{irb.protocol_title}}
                    </h5>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h5>
                        <b-badge variant="secondary">
                            <b-icon icon="building" aria-hidden="true"/>
                            </b-icon> Department
                        </b-badge>
                        {{irb.protocol_department}}
                    </h5>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h5>
                        <b-badge variant="secondary">
                            <b-icon icon="clock-fill" aria-hidden="true"/>
                            </b-icon> Creation Date
                        </b-badge>
                        {{irb.created_at}}
                    </h5>
                </b-col>
            </b-row>

            <b-card bg-variant="light">
                <b-card-text>
                    <p v-html="notifications.project_creation_irb_link_instructions"></p>
                </b-card-text>
            </b-card>
            <hr>
            <b-table striped hover bordered :items="irb.projects" :fields="fields_irb_projects">
                <template #cell(action)="row">
                    <b-button variant="primary" size="sm" @click="requestAccess(row)" class="mr-2">
                        Join
                    </b-button>

                </template>
                <template #bottom-row>
                    <!-- Adding &nbsp; to the cell so that it maintains the standard cell height -->
                    <td v-for="i in fields_irb_projects.length"><span v-if="fields_irb_projects.length == i"><b-button
                                    variant="success" size="sm" @click="onClickNext(5)" class="mr-2">
                        New+
                    </b-button></span>&nbsp;
                    </td>
                </template>
            </b-table>

        </b-card-text>
    </b-card>
</b-container>