<b-container fluid class="mt-3">
    <div class="row">
        <div class="float-right mb-3">
            <b-button size="sm" variant="success" v-b-modal.generic-modal>Add Ticket</b-button>
        </div>
    </div>
    <div class="row">
        <span v-html="tickets_header"></span>
    </div>


    <b-table show-empty striped hover :items="items" :fields="fields" :current-page="currentPage"
             :per-page="perPage"
             :filter="filter" @filtered="onFiltered"
             :empty-text="emptyTicketsTable"
             :empty-filtered-text="emptyFilteredTicketsTable">
        <template #cell(id)="data">
            <span v-html="data.value"></span>
        </template>
        <template #thead-top="data">
            <b-tr style="background-color: #D7D7D7">
                <b-th colspan="2">
                    <b-row>
                        <b-col lg="6">
                            <b-form-checkbox v-model="currentProjectTickets"
                                             name="all-tickets"
                                             id="all-tickets"
                                             value="Yes"
                                             unchecked-value="No" @change="filterTickets($event)">Only show tickets for
                                this
                                project
                            </b-form-checkbox>

                        </b-col>
                        <b-col lg="6">
                            <b-form-checkbox v-model="openTickets"
                                             name="open-tickets"
                                             id="open-tickets"
                                             value="Yes"
                                             unchecked-value="No" @change="filterOpenTickets($event)">Only show open
                                tickets
                            </b-form-checkbox>
                        </b-col>
                    </b-row>
                </b-th>
                <b-th colspan="4">
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
                                <b-button size="sm" :disabled="!filter" @click="filter = ''">Clear</b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form-group>
                </b-th>
            </b-tr>
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