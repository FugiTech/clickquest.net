<template>
  <div class="app">
    <div class="main" @click="click">
      <top />
      <user />
      <pages />
      <bottom />
    </div>
    <div class="modal" v-if="error">
      <div>
        <h1>ERROR</h1>
        <p>{{error}}</p>
        <button @click="setError('')">OK</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

import Top from './Top.vue'
import User from './User.vue'
import Pages from './Pages.vue'
import Bottom from './Bottom.vue'

export default {
  name: 'app',
  components: {
    Top,
    User,
    Pages,
    Bottom
  },
  mounted() {
    document.title = `CLICKQUEST V${this.MAJOR}.${this.MINOR}.${this.PATCH}:${this.REVISION}`
  },
  computed: mapState([
    'MAJOR',
    'MINOR',
    'PATCH',
    'REVISION',
    'error'
  ]),
  methods: mapMutations([
    'click',
    'setError'
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

html, body, .app, .main {
  margin: 0;
  padding: 0;
  height: 100%;
}

.app {
  font-family: "DOS", "Courier", sans-serif;
  background: black;
  color: white;
}

.main {
  display: grid;
  grid-template-rows: min-content minmax(0, auto) min-content;
  grid-template-columns: 50% 50%;
  grid-template-areas:
    "top    top"
    "user   pages"
    "bottom bottom";
}

@media(max-width: 1500px) { .app { font-size: 0.95em; } }
@media(max-width: 1350px) { .app { font-size: 0.90em; } }
@media(max-width: 1290px) { .app { font-size: 0.85em; } }
@media(max-width: 1240px) { .app { font-size: 0.80em; } }
@media(max-width: 1190px) { .app { font-size: 0.75em; } }
@media(max-width: 1130px) { .app { font-size: 0.70em; } }
@media(max-width: 1080px) { .app { font-size: 0.65em; } }

@media(min-width: 1600px) { .user { font-size: 1.5em !important; } }
@media(min-width: 1700px) { .user { font-size: 1.6em !important; } }
@media(min-width: 1800px) { .user { font-size: 1.7em !important; } }
@media(min-width: 1900px) { .user { font-size: 1.8em !important; } }
@media(min-width: 2000px) { .user { font-size: 1.9em !important; } }
@media(min-width: 2100px) { .user { font-size: 2.0em !important; } }

a {
  color: inherit;
}

::-webkit-scrollbar {
  width: 10px;
}
::-webkit-scrollbar-track {
  background: black;
}
::-webkit-scrollbar-thumb {
  background: #333333;
}

::selection {
  background: transparent;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.8);
}
.modal > div {
  width: 20%;
  background: black;
  border: 1px solid white;
  text-align: right;
}
.modal h1 {
  margin: 0;
  border-bottom: 1px solid white;
  text-align: center;
}
.modal p {
  margin: 0;
  padding: 10px 20px;
  text-align: left;
}
.modal button {
  margin: 5px 10px;
  padding: 5px 20px;
  border: 1px solid white;
  background: black;
  color: inherit;
  font-size: 1.2em;
}
</style>
