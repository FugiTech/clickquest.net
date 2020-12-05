import Tab from './Tab.vue'
import Tabs from './Tabs.vue'

export default {
  install (Vue) {
    Vue.component('tab', Tab)
    Vue.component('tabs', Tabs)
  }
}

export { Tab, Tabs }
