<script>

import {useModalStore} from '../../store/modal.js';

export default {
  name: "SupportTickets",
  setup() {
    const modalStore = useModalStore();

    const showModal = (modalId) => {
      modalStore.showModal(modalId);
    };

    return { showModal };
  },
  methods: {
    addTicket() {
      console.log('Add Ticket');
    }
  }
}
</script>

<template>
  <div class="container-fluid">
    <div class="row">
      <div class="float-right mb-3">
        <button @click="showModal('modal1')">Add Ticket</button>
        <button @click="showModal('modal2')">Open Modal 2</button>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>