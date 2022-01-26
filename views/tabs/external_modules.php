<b-container fluid class="mt-3">
    <b-row>
        <b-col lg="12">
            <b-alert :variant="EMVariant"
                     dismissible
                     fade
                     :show="showEMDismissibleAlert"
            ><i class="fas fa-exclamation-circle"></i>
                <span v-html="EMAlertMessage"></span>
            </b-alert>
        </b-col>
    </b-row>
    <div class="row">
        <span v-html="external_modules_header"></span>
    </div>


    <b-row class="mb-3 mt-3">
        <b-col lg="6" class="ml-2">
            <span v-html="notifications.update_em_list"></span>
        </b-col>
    </b-row>

    <b-row class="mb-3 mt-3">
        <b-col lg="6">
            <b-button size="sm" variant="success" :disabled='isDisabled' @click="updateExternalModuleList()">Update
                External Modules List
            </b-button>
        </b-col>
    </b-row>


    <b-table striped hover :items="items_em" :fields="fields_em" :current-page="currentPage_em"
             :per-page="perPage_em"
             :filter="filter_em" @filtered="onFilteredEM">
        <!--    <template #cell(id)="data">-->
        <!--        <span v-html="data.value"></span>-->
        <!--    </template>-->

        <template #thead-top="data">
            <b-tr style="background-color: #D7D7D7">
                <b-th>
                    <b-row>
                        <b-col lg="12">
                            <b-form-checkbox v-model="currentProjectEms"
                                             name="all-ems"
                                             id="all-ems"
                                             value="Yes"
                                             unchecked-value="No" @change="filterEms($event)">Show only External Modules
                                with Fees
                            </b-form-checkbox>

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
                                    v-model="filter_em"
                                    type="search"
                                    placeholder="Type to Search"
                            ></b-form-input>

                            <b-input-group-append>
                                <b-button size="sm" :disabled="!filter_em" @click="filter_em = ''">Clear</b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-th>
            </b-tr>
        </template>
        <!-- render html for this column -->
        <template #cell(maintenance_monthly_cost)="data">
            <span v-html="data.value"></span>
        </template>
    </b-table>

    <b-row class="mt-2">
        <b-col class="float-left" md="3">
            Monthly Total: <strong>${{totalFees}}</strong>
        </b-col>
        <b-col offset="3" sm="6" md="6" class="my-1">
            <b-pagination
                    v-model="currentPage_em"
                    :total-rows="totalRows_em"
                    :per-page="perPage_em"
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