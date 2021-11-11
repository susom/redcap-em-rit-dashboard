<template>
  <div id="app">
    <h1>Project id is: {{this.pid}}</h1>
    <Test :redcap="redcapPid"></Test>
  </div>
</template>

<script>
import Test from './components/Test'
export default {
  name: 'App',
  components:{
    'Test' : Test
  },
  props: ['pid'],
  data(){
    return {
      'redcapPid': '123'
    }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
