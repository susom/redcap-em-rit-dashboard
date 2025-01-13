<template>
  <div>
    <!-- Modal 1 -->
    <div
      class="modal fade"
      id="support-ticket-modal"
      tabindex="-1"
      aria-labelledby="modal1Label"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal1Label">Create New Ticket</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">MODAL TODO</div>
        </div>
      </div>
    </div>

    <!-- Modal 2 -->
    <div
      class="modal fade"
      id="modal2"
      tabindex="-1"
      aria-labelledby="modal2Label"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal2Label">Modal 2</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">This is Modal 2 content.</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModalComponent',
};
</script>
