import { createApp } from 'vue'
import VueHead from 'vue-head'
import VueMoment from 'vue-moment'
import VueChatScroll from 'vue-chat-scroll'

import App from './App.vue'
import store from './store'
import Tabs from './tabs'
import './conn' // Registers itself in send.js

const app = createApp(App)
  .use(VueHead)
  .use(VueChatScroll)
  .use(store)
  .use(Tabs)


const $clicksForLevel = (level, hardcore) => {
  let initial = hardcore ? 200 : 100
  let rate = hardcore ? 1.110316 : 1.0906595 // matches faulty behavior of Clickquest v1
  return Math.round(initial * (1 - Math.pow(rate, level)) / (1 - rate))
}

app.config.globalProperties = {
  $store: store,

  $commaify: (value) => value ? value.toLocaleString('en-US') : '',

  $clicksForLevel,

  $increaseForLevel: (level, hardcore) => {
    return $clicksForLevel(level, hardcore) - $clicksForLevel(level - 1, hardcore)
  },

  $levelForClicks: (clicks, hardcore) => {
    let initial = hardcore ? 200 : 100
    let rate = hardcore ? 1.110316 : 1.0906595 // matches faulty behavior of Clickquest v1
    return Math.floor(Math.log(1 + (rate - 1) * (clicks + 0.5) / initial) / Math.log(rate))
  },
}

// Fake install since VueMoment doesn't support Vue3 yet
VueMoment.install({
  prototype: {},
  filter(name, fn) {
    app.config.globalProperties[`$${name}`] = fn
  },
})

app.mount('body')
