import Vue from 'vue'
import App from './App.vue'

Vue.config.productionTip = false
Vue.config.devtools = true
new Vue({
    el: 'app',
    render(h) {
        return h(App, {
            props: {
                pid: this.$el.attributes.pid.value
            }
        })
    }
})