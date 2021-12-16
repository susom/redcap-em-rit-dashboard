<template>
  <div>
    <b-tabs content-class="mt-3">
      <b-tab title="R2P2" active>
        <r2p2></r2p2>
      </b-tab>
      <b-tab title="Support">
        <support></support>
      </b-tab>
      <b-tab title="External Modules">
        <external-modules></external-modules>
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>

import r2p2 from './tabs/r2p2'
import support from './tabs/support'
import External_modules from "@/components/tabs/external_modules";

export default {
  name: "tabs",
  components: {
    External_modules,
    "r2p2": r2p2,
    "support": support,
    "external-modules": External_modules
  }
}
</script>

<style scoped>

</style>