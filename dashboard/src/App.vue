<template>
  <div id="app" class="container">
    <h1>{{ this.pid }}</h1>
    <test :pid="this.pid"></test>
  </div>
</template>

<script>
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import Test from './components/Test'

export default {
  name: 'App',
  components: {
    'test': Test
  },
  props: {
    pid: String
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
