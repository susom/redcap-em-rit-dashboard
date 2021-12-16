<template>
  <div>
    Test component
    <div class="alert alert-success">{{ this.pid }}</div>
  </div>
</template>

<script>
export default {
  name: "Test",
  props: ['pid']
}
</script>

<style scoped>

</style>