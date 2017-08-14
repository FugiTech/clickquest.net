<template>
  <div class="app" @click="click">
    <top />
    <middle />
    <bottom />
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import Top from './Top'
import Middle from './Middle'
import Bottom from './Bottom'

export default {
  name: 'app',
  components: {
    Top,
    Middle,
    Bottom
  },
  head: {
    title: function () {
      return {
        inner: `CLICKQUEST V${this.MAJOR}.${this.MINOR}.${this.PATCH}:${this.REVISION}`
      }
    }
  },
  computed: mapState([
    'MAJOR',
    'MINOR',
    'PATCH',
    'REVISION'
  ]),
  methods: mapMutations([
    'click'
  ])
}
</script>

<style>
@font-face {
	font-family: 'DOS';
	src: url('assets/dos.eot');
	src: local('☺'), url('assets/dos.woff') format('woff'), url('assets/dos.ttf') format('truetype'), url('assets/dos.svg#webfontGl3kt1Pv') format('svg');
	font-weight: normal;
	font-style: normal;
}

html, body, .app {
  margin: 0;
  padding: 0;
  height: 100%;
}

.app {
  display: flex;
  flex-direction: column;
  font-family: "DOS", "Courier", sans-serif;
  background: black;
  color: white;
}

a {
  color: inherit;
}
</style>
