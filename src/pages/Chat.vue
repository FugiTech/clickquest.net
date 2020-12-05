<template>
  <div class="chat">
    <chatlist :chat="chat"></chatlist>
    <form class="sender" v-show="isLoggedIn" @submit.prevent="sendMessage">
      <input type="text" class="mes">
      <input type="submit" class="messend" value="Send">
    </form>
    <span class="help" v-show="isLoggedIn"><b>*bold*</b> <i>_italic_</i> <a href="#" @click.prevent>[link text](url)</a> (or just type a URL) \escape</span>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import chatlist from '../components/chatlist.vue'

export default {
  name: 'chat',
  components: {
    chatlist
  },
  computed: mapState([
    'isLoggedIn',
    'chat',
    'send'
  ]),
  methods: {
    sendMessage: function (e) {
      let message = e.target.elements[0].value
      e.target.elements[0].value = ''
      this.send('chat', {message: message})
    }
  }
}
</script>

<style scoped>
.chat {
  display: flex;
  flex-direction: column;
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

.help {
  color: #333;
}
</style>
