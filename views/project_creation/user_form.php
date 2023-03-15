<b-container fluid class="mt-3">
    <strong>{{notifications.project_creation_user_form_title}}</strong>
    <p v-html="notifications.project_creation_user_form_body"></p>
    <hr>
    <!-- Using slots -->
    <p><strong>{{notifications.project_creation_user_form_instruction}}</strong><br></p>
    <b-input-group class="mt-3">

        <v-select label="fullname" :filterable="false" class="col-8 nopadding" @search="searchUser"
                  :options="search_users" @input="selectUser">
            <template slot="no-options">
                Search user name, sunet, email...
            </template>
            <template slot="option" slot-scope="option">
                <div style="display: inline;"><strong style="font-size: 14px;">{{option.fullname}}</strong> -
                    ({{option.suid}})
                    <div class="meta-text"><small>{{option.affiliate}} |
                            {{option.email}}</small></div>
                </div>
            </template>
            <template slot="selected-option" slot-scope="option">
                <div class="selected d-center">
                    {{option.fullname}} | {{option.email}}
                </div>
            </template>
        </v-select>
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
        <b-card-text>


            <b-card bg-variant="light">
                <b-card-text>
                    <p v-html="notifications.project_creation_irb_link_instructions"></p>
                </b-card-text>
            </b-card>
            <hr>
            <b-table striped hover bordered :items="user_projects" :fields="fields_irb_projects">
                <template #cell(action)="row">
                    <b-button variant="primary" size="sm" @click="requestAccess(row)" class="mr-2">
                        Join
                    </b-button>

                </template>
                <template #bottom-row>
                    <!-- Adding &nbsp; to the cell so that it maintains the standard cell height -->
                    <td v-for="i in fields_irb_projects.length"><span v-if="fields_irb_projects.length == i"><b-button
                                    variant="success" size="sm" @click="onClickNext(4)" class="mr-2">
                        New+
                    </b-button></span>&nbsp;
                    </td>
                </template>
            </b-table>

        </b-card-text>
    </b-card>
</b-container>