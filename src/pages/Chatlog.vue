<template>
  <div class="chatlog">
    <div class="logctrl">
      <input type="button" value="❘<" @click.prevent="page=1" />
      <input type="button" value="<<<" @click.prevent="page-=25" />
      <input type="button" value="<<" @click.prevent="page-=5" />
      <input type="button" value="<" @click.prevent="page--" />
      <input type="number" v-model.lazy.trim.number="page" />
      <span>/</span>
      <span class="wide">{{$commaify(lastPage)}}</span>
      <input type="button" value=">" @click.prevent="page++" />
      <input type="button" value=">>" @click.prevent="page+=5" />
      <input type="button" value=">>>" @click.prevent="page+=25" />
        <input type="button" value=">❘" @click.prevent="page=lastPage" />
    </div>
    <chatlist ref="chatlist" :chat="chat"></chatlist>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import chatlist from '../components/chatlist.vue'

export default {
  name: 'chatlog',
  components: {
    chatlist
  },
  data: function () {
    return {
      page: 0,
      lastPage: 0,
      chat: []
    }
  },
  computed: mapState([
    'send'
  ]),
  mounted: function () {
    this.page = 1 // Ensure watch.page fires on creation
  },
  watch: {
    page: function () {
      if (this.page < 1) this.page = 1
      if (this.lastPage && this.page > this.lastPage) this.page = this.lastPage
      this.send('chatlog', {page: +this.page}).then((data) => {
        if (data.error) {
          console.error(data.error)
          return
        }

        this.chat = data.chat
        this.lastPage = data.pages
        this.$refs.chatlist.scrollTop = 0
      })
    }
  }
}
</script>

<style scoped>
.chatlog {
  display: flex;
  flex-direction: column;
  flex: auto;
}

.logctrl {
  display: flex;
  padding: 10px 0;
  align-items: center;
  justify-content: center;
}

input {
  border: 1px solid #333333;
  background: black;
  color: inherit;
  margin: 6px;
}

input[type="number"] {
  text-align: right;
  width: 60px;
}

span {
  display: block;
  margin: 6px;
  line-height: 16px;
}

input, .wide {
  flex: 0 1 60px;
}

</style>
