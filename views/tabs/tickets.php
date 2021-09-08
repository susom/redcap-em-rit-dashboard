<b-container>
    <div class="row">
        <div class="float-right mb-3">
            <b-button variant="success" v-b-modal.generic-modal>Add Ticket</b-button>
        </div>
    </div>
    <div class="row">
        <span v-html="tickets_header"></span>
    </div>
    <b-row align-h="start">
        <b-col lg="6" class="my-1">
            <b-form-group
                    label-for="filter-input"
                    label-size="sm"
                    class="mb-0"
            >
                <b-input-group size="sm">
                    <b-form-input
                            id="filter-input"
                            v-model="filter"
                            type="search"
                            placeholder="Type to Search"
                    ></b-form-input>

                    <b-input-group-append>
                        <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-form-group>
        </b-col>
        <b-col lg="6" class="my-1">
            <b-form-group
                    description="Display all users tickets including ticket for different REDCap projects. "
                    label-cols-sm="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                    v-slot="{ ariaDescribedby }"
            >

                <b-form-checkbox v-model="currentProjectTickets"
                                 name="all-tickets"
                                 id="all-tickets"
                                 value="Yes"
                                 unchecked-value="No" @change="filterTickets($event)">Display Tickets for current REDCap
                    projects
                </b-form-checkbox>

            </b-form-group>
        </b-col>
    </b-row>

    <b-table show-empty striped hover :items="items" :fields="fields" :current-page="currentPage"
             :per-page="perPage"
             :filter="filter" @filtered="onFiltered"
             :empty-text="emptyTicketsTable"
             :empty-filtered-text="emptyFilteredTicketsTable">
        <template #cell(id)="data">
            <span v-html="data.value"></span>
        </template>
    </b-table>
    <b-row align-h="end">
        <b-col sm="7" md="6" class="my-1 align-right">
            <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    align="fill"
                    size="sm"
                    class="my-0"
            ></b-pagination>
        </b-col>
    </b-row>
</b-container>