<div class="row">
    <div class="float-right mb-3">
        <b-button v-b-modal.generic-modal>Add Ticket</b-button>
    </div>
</div>
<div class="row">
    <span v-html="tickets_header"></span>
</div>
<b-row>
    <b-col lg="6" class="my-1">
        <b-form-group
                label="Filter"
                label-for="filter-input"
                label-cols-sm="3"
                label-align-sm="right"
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
    <b-col sm="7" md="6" class="my-1">
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
<b-table striped hover :items="items" :fields="fields" :current-page="currentPage"
         :per-page="perPage"
         :filter="filter" @filtered="onFiltered">
    <template #cell(id)="data">
        <span v-html="data.value"></span>
    </template>
</b-table>
