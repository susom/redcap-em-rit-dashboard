<b-container fluid class="mt-3">
    <b-row>
        <b-alert :variant="EMVariant"
                 dismissible
                 fade
                 :show="showEMDismissibleAlert"
        ><i class="fas fa-exclamation-circle"></i>
            {{EMAlertMessage}}
        </b-alert>
    </b-row>
    <div class="row">
        <span v-html="external_modules_header"></span>
    </div>

    <b-table striped hover :items="items_em" :fields="fields_em" :current-page="currentPage_em"
             :per-page="perPage_em"
             :filter="filter_em" @filtered="onFilteredEM">
        <!--    <template #cell(id)="data">-->
        <!--        <span v-html="data.value"></span>-->
        <!--    </template>-->

        <template #thead-top="data">
            <b-tr style="background-color: #D7D7D7">
                <b-th></b-th>
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
    </b-table>
    <b-row>

    </b-row>
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

    <b-row v-if="determineREDCapStep() == 4" class="mt-2">
        <b-col md="12"><p>REDCap Maintenance Agreement created for this project. Click
                <a target="_blank" :href="portalREDCapMaintenanceAgreement.link"><i
                            class="fas fa-external-link-alt"></i> here</a> to access the SOW</p>
        </b-col>

    </b-row>

</b-container>