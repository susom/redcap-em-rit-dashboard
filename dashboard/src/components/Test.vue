<template>
  <div>
    Test component
    <div class="alert alert-dark">{{ this.pid }}</div>
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