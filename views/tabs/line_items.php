<b-container fluid class="mt-3">
    <b-row>
        <b-col lg="12">
            <b-alert :variant="line_items_variant"
                     dismissible
                     fade
                     :show="show_line_items_dismissibleAlert"
            ><i class="fas fa-exclamation-circle"></i>
                <span v-html="line_items_alert_message"></span>
            </b-alert>
        </b-col>
    </b-row>
    <div class="row">
        <span v-html="line_items_header"></span>
    </div>

    <b-table striped hover :items="items_line_items" :fields="fields_line_items" :current-page="current_page_line_items"
             :per-page="per_page_line_items"
             :filter="filter_line_items" @filtered="onFilteredLineItems">
        <!--    <template #cell(id)="data">-->
        <!--        <span v-html="data.value"></span>-->
        <!--    </template>-->
        <template #cell(sow_title)="data">
            <span v-html="data.value"></span>
        </template>
        <template #thead-top="data">
            <b-tr style="background-color: #D7D7D7">
                <b-th colspan="5">
                    <b-row>
                        <b-col lg="12">
                            <!--                            <b-form-checkbox v-model="current_project_line_items"-->
                            <!--                                             name="all-ems"-->
                            <!--                                             id="all-ems"-->
                            <!--                                             value="Yes"-->
                            <!--                                             unchecked-value="No" @change="filterEms($event)">Show only External Modules-->
                            <!--                                with Fees-->
                            <!--                            </b-form-checkbox>-->

                        </b-col>
                    </b-row>
                </b-th>
                <b-th>
                    <b-form-group
                            label-for="filter-input"
                            label-size="sm"
                            class="mb-0"
                    >
                        <b-input-group size="sm">
                            <b-form-input
                                    id="filter-input"
                                    v-model="filter_line_items"
                                    type="search"
                                    placeholder="Type to Search"
                            ></b-form-input>

                            <b-input-group-append>
                                <b-button size="sm" :disabled="!filter_line_items" @click="filter_line_items = ''">
                                    Clear
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-th>
            </b-tr>
        </template>

    </b-table>

    <b-row class="mt-2">
        <b-col class="float-left" md="3">
            <!--            Monthly Total: <strong>${{totalFees}}</strong>-->
        </b-col>
        <b-col offset="3" sm="6" md="6" class="my-1">
            <b-pagination
                    v-model="current_page_line_items"
                    :total-rows="total_rows_line_items"
                    :per-page="per_page_line_items"
                    align="fill"
                    size="sm"
                    class="my-0"
            ></b-pagination>
        </b-col>
    </b-row>
    <b-alert v-if="determineREDCapStep() == 4" class="d-flex d-inline-block"
             variant="success"
             fade
             show
    >
        <b-row class="mt-2">

            <b-col class="justify-content-center align-self-center" lg="12"><h5
                        class="d-inline-block  p-1"><i
                            class="fa fa-check-circle"></i></h5> This project is properly configured with an approved
                REDCap
                Maintenance Agreement. Click <a target="_blank" :href="portalREDCapMaintenanceAgreement.link"><i
                            class="fas fa-external-link-alt"></i> here</a> to access the SOW
            </b-col>
        </b-row>
    </b-alert>

    <b-row>
        <b-col>The data for this table is refreshed daily. Recent changes may not be reflected in the table for 24
            hours. Refresh your project <a href="#">Feature in development.</a></b-col>
    </b-row>

</b-container>