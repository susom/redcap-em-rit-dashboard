<template>
  <div>
    <h1>Support</h1>
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
          <span v-html="data.value"></span>
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
    <b-modal ref="generic-modal" size="lg" id="generic-modal" title="Create New Ticket">
      <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <div class="container-fluid">
          <div class="alert hidden messages"></div>
          <form id="jira-ticket">

            <div class="form-group">
              <label id="exampleInputEmail1"><strong>Title</strong></label>
              <!--            <input type="text" class="form-control" id="summary" name="summary" aria-describedby="emailHelp"-->
              <!--                   placeholder="Question Summary" required>-->
              <b-form-input v-model="ticket.summary" max="250" placeholder="Ticket Title" required></b-form-input>
            </div>
            <div class="form-group">
              <label id="portal-projects"><strong>R2P2 Project</strong> (<span>To create new R2P2 project click <a
                  target="_blank" :href="base_portal_url">here</a></span>)</label>
              <!--            <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"-->
              <!--                           value-field="id"-->
              <!--                           text-field="project_name">-->
              <!--            </b-form-select>-->
              <v-select class="mb-3 nopadding" v-model="ticket.project_portal_id" :options="portal_projects_list"
                        :reduce="project_portal_id => project_portal_id.id"
                        label="project_name">
              </v-select>

            </div>
            <!--        <div class="form-group">-->
            <!--            <label for="issue-types">Issue Type</label>-->
            <!--            <b-form-select v-model="ticket.type" :options="ticket_types" class="mb-3">-->
            <!--            </b-form-select>-->
            <!--        </div>-->

            <div class="form-group">
              <label id="description"><strong>Detailed Description</strong></label>
              <b-form-textarea
                  id="textarea"
                  v-model="ticket.description"
                  placeholder="Screenshots and/or Attachments can be added as comment after creating the ticket"
                  rows="6"
                  max-rows="10"
              ></b-form-textarea>
            </div>
          </form>
        </div>
      </b-overlay>
      <template #modal-footer="{ ok, cancel }">
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button :disabled='isDisabled' variant="primary" @click="submitTicket()">
          Submit
        </b-button>
        <b-button :disabled='isDisabled' variant="danger" @click="cancel()">
          Cancel
        </b-button>
      </template>
    </b-modal>
    <b-modal ref="ticket-modal" size="lg" id="ticket-modal" title="Support Ticket">
      <div class="d-block text-center">
        <span class="row ml-2" v-html="bodyMessage"></span>
      </div>
      <template #modal-footer="{ ok, cancel }">
        <b-button :disabled='isDisabled' variant="secondary" @click="cancel()">
          Close
        </b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import axios from "axios";
import vSelect from 'vue-select'

import 'vue-select/dist/vue-select.css';

export default {
  name: "support",
  components: {
    vSelect
  },
  data() {
    return {
      filter: null,
      bodyMessage: '',
      ticket: {
        project_portal_id: '',
        summary: '',
        description: ''
      },
      data: [],
      items: [],
      allItems: [],
      totalRows: 0,
      currentProjectTickets: 'Yes',
      openTickets: 'Yes',
      emptyTicketsTable: "",
      alertMessage: "",
      emptyFilteredTicketsTable: "No tickets attached to this REDCap Project. To See full list uncheck 'Display Tickets for current REDCap projects' checkbox",
      fields: [
        {
          key: 'id',
          sortable: true
        },
        {
          key: 'title',
          sortable: true
        },
        // {
        //     key: 'type',
        //     sortable: true
        // },
        {
          key: 'status',
          sortable: true
        },
        {
          key: 'created_at',
          sortable: true
        },
        {
          key: 'modified_at',
          sortable: true
        },
        {
          key: 'redcap_pid',
          label: 'REDCap PID',
          sortable: true
        }
      ],
      currentPage: 1,
      perPage: 100,
    }
  },
  methods: {
    filterOpenTickets() {
      if (this.openTickets === 'Yes') {
        this.items = this.allItems.filter(function (n) {
          return n.status !== 'Done';
        });
      } else {
        this.filterTickets()
      }

    },
    getUserTickets: function () {
      this.emptyTicketsTable = 'No Tickets for this REDCap project'
      axios.get(this.ajaxUserTicketURL)
          .then(response => {
            this.items = this.allItems = response.data.data;
            this.totalRows = this.items.length
            if (this.items.length == undefined || this.items.length == 0) {
              this.emptyTicketsTable = 'No Tickets Found'
            }
            this.filterTickets()
          });
    },
    onFiltered(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRows = filteredItems.length
      this.currentPage = 1
    },
    filterTickets() {
      if (this.currentProjectTickets === 'Yes') {
        this.items = this.allItems.filter(function (n) {
          return n.current_pid === true;
        });
      } else {
        this.items = this.allItems
      }

    },
    cancel: function () {
      this.$refs['generic-modal'].hide()
    },
    submitTicket: function () {
      axios.post(this.ajaxCreateJiraTicketURL, this.ticket)
          .then(response => {
            this.getUserTickets()
            this.$refs['generic-modal'].hide()
            this.$refs['ticket-modal'].show()
            this.bodyMessage = response.data.message
            this.$emit('globalMessage', 'success', response.data.message, true)
          }).catch(err => {
        this.variant = 'danger'
        this.showDismissibleAlert = true
        this.alertMessage = err.response.data.message
      });
    },
  },
  props: {
    ajaxUserTicketURL: String,
    ajaxCreateJiraTicketURL: String,
    tickets_header: String,
    base_portal_url: String,
    isDisabled: Boolean,
    isLoading: Boolean,
    portal_projects_list: Array,
  },
  mounted() {
    this.getUserTickets();
  }
}
</script>

<style scoped>

</style>