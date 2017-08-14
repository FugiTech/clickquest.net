<template>
  <div class="chat">
    <div class="messages"></div>
    <form class="sender" v-show="isLoggedIn" @submit.prevent="sendMessage">
      <input type="text" class="mes">
      <input type="submit" class="messend" value="Send">
    </form>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'chat',
  computed: mapState([
    'isLoggedIn'
  ]),
  methods: mapActions([
    'sendMessage'
  ])
}
</script>

<style scoped>
.chat {
  display: flex;
  flex-direction: column;
  flex: auto;
}

.messages {
  flex: auto;
}

.sender {
  display: flex;
  flex: none;
}

.mes { flex: auto; }
.messend { flex: none; }
.mes, .messend {
  background: black;
  border: 1px solid gray;
  color: white;
  font-size: 1em;
}
</style>
