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
      id="professional-services-modal"
      tabindex="-1"
      aria-labelledby="professional-services-modalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="professional-services-modalLabel">Generate Support Block</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">Modal Todo</div>
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
