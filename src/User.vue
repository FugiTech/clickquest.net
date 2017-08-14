<template>
  <div class="user">
    <template v-if="isLoggedIn">
      <div class="progress" :style="userStyle">
        <div class="name">{{ userTitle }}</div>
        <div class="level">{{ user.level }}</div>
        <div class="total">total: {{ user.clicks }} clicks</div>
        <div class="remain">next: {{ nextClicks }} clicks</div>
        <div class="bonus"></div>
      </div>
      <div class="server">
        <div>
          <div class="ip">SERVER IP: 136.24.49.227</div>
          <div class="logintime">ON SINCE: 11:11PM 8/8/2017</div>
        </div>
        <div>
          <div class="session">SESSION TIME: 0:02:03</div>
          <div class="totaltime">TOTAL TIME: 128:03:17</div>
        </div>
      </div>
    </template>
    <div class="login" v-else>
      <form @submit.prevent="login">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <input type="submit" value="Login"><input type="reset" value="Clear">
      </form>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default {
  name: 'user',
  computed: {
    ...mapState([
      'isLoggedIn',
      'user'
    ]),
    ...mapGetters([
      'userTitle',
      'userStyle',
      'nextClicks'
    ])
  },
  methods: mapActions([
    'login'
  ])
}
</script>

<style scoped>
.user {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 50%;
  font-size: 1.4em;
  text-align: center;
}

.login input {
  border: 1px solid white;
  background: black;
  color: white;
  margin: 8px 20px;
  padding: 8px;
  font-size: 1.2em;
}

.name { font-size: 2em; }
.level { font-size: 8em; line-height: 0.8em; }

.server {
  display: flex;
  margin-top: 20px;
}
.server > div {
  display: flex;
  flex-direction: column;
  margin: 0 20px;
}
.ip, .logintime { text-align: left; }
.session, .totaltime { text-align: right; }

</style>
