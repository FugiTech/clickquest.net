import Vue from 'vue'
import VueHead from 'vue-head'
import VueMoment from 'vue-moment'
import VueChatScroll from 'vue-chat-scroll'

import App from './App'
import store from './store'
import Tabs from './tabs'
import './conn' // Registers itself in send.js

Vue.config.productionTip = false

Vue.use(VueHead)
Vue.use(VueMoment)
Vue.use(VueChatScroll)
Vue.use(Tabs)

Vue.filter('commaify', (value) => {
  return value ? value.toLocaleString('en-US') : ''
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  render: h => h(App),
  store
})
