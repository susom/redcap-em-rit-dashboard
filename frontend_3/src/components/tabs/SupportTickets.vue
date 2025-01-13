<script>

import {useModalStore} from '../../store/modal.js';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import axios from 'axios';

DataTable.use(DataTablesCore);
DataTablesCore.Responsive.bootstrap(bootstrap);
export default {
  name: "SupportTickets",
  components: {
    DataTable
  },
  data() {
    return {
      tickets_header: window.tickets_header || '',
      ajax_urls: window.ajax_urls || {},
      support_tickets: [],
      all_support_tickets: [],
      loading: false,
      empty_tickets_table_message: '',
      support_ticket_fields: [
        {
          data: 'id',
          sortable: true
        },
        {
          data: 'title',
          sortable: true
        },
        {
          data: 'status',
          sortable: true
        },
        {
          data: 'created_at',
          sortable: true
        },
        {
          data: 'modified_at',
          sortable: true
        },
        {
          data: 'redcap_pid',
          label: 'REDCap PID',
          sortable: true
        }
      ],
    };
  },
  setup() {
    const modalStore = useModalStore();

    const showModal = (modalId) => {
      modalStore.showModal(modalId);
    };

    return {showModal};
  },
  mounted() {
    this.getSupportTickets();
  },
  methods: {
    addTicket() {
      console.log('Add Ticket');
    },
    async getSupportTickets() {
      try {
        this.loading = true
        const response = await axios.get(this.ajax_urls.get_user_tickets);

        this.support_tickets = this.all_support_tickets = response.data.data
      } catch (error) {
        console.error('Error fetching tickets:', error);
      }finally {
        this.loading = false; // End loading
      }
    },
  }
}

</script>

<template>
  <div class="container-fluid">
    <div class="row d-flex">
      <div class="float-left mb-3">
        <button class="btn btn-success btn-sm" @click="showModal('support-ticket-modal')">Add Ticket</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <span v-html="this.tickets_header"></span>
      </div>
    </div>
    <div class="row">

      <div class="col-12">
        <DataTable
            :columns="this.support_ticket_fields"
            :data="this.support_tickets"
            :options="{
            responsive: true,
            language: {
              loadingRecords: 'Loading tickets, please wait...', // Custom loading message
              emptyTable: loading ? 'Loading tickets...' : 'No tickets available',
                },
              }"
            class="table table-hover table-striped nowrap"
        >
          <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Modified At</th>
            <th>REDCap PID</th>
          </tr>
          </thead>
        </DataTable>
      </div>
    </div>
  </div>
</template>

<style>
@import 'bootstrap';
@import 'datatables.net-bs5';
@import 'datatables.net-responsive-bs5';
</style>