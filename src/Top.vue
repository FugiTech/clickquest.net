<template>
  <div class="top">
    <div class="title">
      <div class="version">CLICKQUEST VERSION {{MAJOR}} PATCH {{MINOR}} REVISION {{PATCH}}.{{REVISION}}</div>
      <div class="copyright">Copyright 2010-20 Bionic Trousers / Exim Works</div>
    </div>
    <div class="links">
      <div class="twitter">Twitter:
        <a href="https://twitter.com/fugiman" target="_blank">Fugi</a>
        <a href="https://twitter.com/alexsteacy" target="_blank">Alex Steacy</a>
        <a href="https://twitter.com/loadingreadyrun" target="_blank">LRR</a>
      </div>
      <div class="web">Web:
        <a href="http://voxlunch.tumblr.com/" target="_blank">Eximworks</a>
        <a href="http://loadingreadyrun.com/" target="_blank">LRR</a>
        <a href="https://www.youtube.com/watch?v=ogR0f5txRzE&list=PLV_qemO0oathu1wV9vXDOomhNSxCMJNQE" target="_blank">CrapShots</a>
      </div>
    </div>
    <div class="logout" v-if="isLoggedIn" @click="logout">
      > Logout
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'top',
  computed: mapState([
    'MAJOR',
    'MINOR',
    'PATCH',
    'REVISION',
    'isLoggedIn'
  ]),
  methods: mapActions([
    'logout'
  ])
}
</script>

<style scoped>
.top {
  grid-area: top;
  display: flex;
  padding: 15px 50px;
}

.title {
  flex: auto;
	font-size: 1.8em;
}

.links {
  font-size: 1.2em;
}

.links a {
  margin-left: 6px;
}

.logout {
  flex: none;
  margin-left: 30px;
  font-size: 2.4em;
  cursor: pointer;
}
</style>
