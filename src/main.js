import Vue from 'vue'
import VueHead from 'vue-head'

import App from './App'
import store from './store'
import Tabs from './tabs'

Vue.config.productionTip = false

Vue.use(VueHead)
Vue.use(Tabs)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  render: h => h(App),
  store
})
