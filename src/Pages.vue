<template>
  <div class="pages">
    <tabs>
      <tab name="Chat"><chat /></tab>
      <tab name="Statistics"><statistics /></tab>
      <tab name="Level List"><levelList /></tab>
      <tab name="Changelog"><changelog /></tab>
      <tab name="Color Select" :show="level >= 75"><colorList /></tab>
      <tab name="Chatlog"><chatlog /></tab>
    </tabs>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Chat from './pages/Chat'
import Statistics from './pages/Statistics'
import LevelList from './pages/LevelList'
import Changelog from './pages/Changelog'
import ColorList from './pages/ColorList'
import Chatlog from './pages/Chatlog'

export default {
  name: 'pages',
  components: {
    Chat,
    Statistics,
    LevelList,
    Changelog,
    ColorList,
    Chatlog
  },
  computed: {
    ...mapGetters([
      'level'
    ])
  }
}
</script>

<style scoped>
.pages {
  flex: 50%;
  padding: 0 20px;
}
</style>

<style>
.tabs {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.tabs > ul {
  list-style: none;
  display: flex;
  flex: none;
  margin: 0;
  padding: 0;
  border-bottom: 1px solid white;
}

.tabs > ul li {
  margin-right: 4px;
  border: 1px solid white;
  border-bottom: 0;
  white-space: nowrap;
}

.tabs > ul li a {
  display: flex;
  padding: 8px 16px;
  text-decoration: none;
}

.tabs > ul li:hover {
  border: 1px solid #999;
  border-bottom: 0;
  background: #333;
}

.tabs > ul li.active {
  background: white;
  color: black;
}

.tabs > div {
  display: flex;
  flex: auto;
  overflow: auto;
}

.tabs > div > section {
  display: flex;
  flex-direction: column;
  flex: auto;
}
</style>
