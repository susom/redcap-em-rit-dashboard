<template>
  <div class="container">
      <OverlayComponent/>
      <HeaderComponent/>
      <GlobalOverlay/>
      <MainComponent/>
      <FooterComponent/>
      <ModalComponent/>
  </div>
</template>

<script>
import HeaderComponent from './components/HeaderComponent.vue'
import GlobalOverlay from './components/GlobalOverlay.vue';
import MainComponent from "./components/MainComponent.vue";
import FooterComponent from "./components/FooterComponent.vue";
import ModalComponent from "./components/ModalComponent.vue";
import OverlayComponent from "./components/OverlayComponent.vue";
export default {
  name: 'App',
  components: {
    OverlayComponent,
    FooterComponent,
    MainComponent,
    HeaderComponent,
    GlobalOverlay,
    ModalComponent,
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
