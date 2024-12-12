<template>
  <div class="container">
      <HeaderComponent/>
      <GlobalOverlay/>
      <MainComponent/>
      <FooterComponent/>
  </div>
</template>

<script>
import HeaderComponent from '@/components/HeaderComponent.vue'
import GlobalOverlay from '@/components/GlobalOverlay.vue';
import MainComponent from "@/components/MainComponent.vue";
import FooterComponent from "@/components/FooterComponent.vue";
export default {
  name: 'App',
  components: {
    FooterComponent,
    MainComponent,
    HeaderComponent,
    GlobalOverlay,
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
