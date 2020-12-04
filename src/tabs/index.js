import Tab from './Tab'
import Tabs from './Tabs'

export default {
  install (Vue) {
    Vue.component('tab', Tab)
    Vue.component('tabs', Tabs)
  }
}

export { Tab, Tabs }
