<template>
  <div class="user">
    <template v-if="isLoggedIn">
      <div class="progress" :style="{color: user.color}" :class="{ glitch: showGlitch }">
        <div class="name">{{ user.hardcore ? '[HARDCORE] ' : '' }}{{ user.name }}</div>
        <div class="level">{{ level }}</div>
        <div class="total">total: {{ user.clicks }} clicks</div>
        <div class="remain">next: {{ nextClicks }} clicks</div>
        <div class="bonus">
          <div v-if="level == 100 && user.hardcore == 0">
            <div v-if="user.clicks < 6500000"><!-- 6477k - 6500k -->
              Congrats on making Level 100! You've won! Feel free to stop clicking whenever you want.
            </div><div v-else-if="user.clicks < 6525000"><!-- 6500k - 6525k -->
              OK, look, I'm starting to get concerned. You beat the game. Why are you still clicking?
            </div><div v-else-if="user.clicks < 6550000"><!-- 6525k - 6550k -->
              I'll be honest, I have a selfish desire for you to stop.
            </div><div v-else-if="user.clicks < 6575000"><!-- 6550k - 6575k -->
              See, I didn't plan for people to click this much, so the game will break...
            </div><div v-else-if="user.clicks < 6600000"><!-- 6575k - 6600k -->
              Thankfully, only your character will break. Everyone else will be fine.
            </div><div v-else-if="user.clicks < 6610000"><!-- 6600k - 6610k -->
              The breaking point is at 6,666,666 clicks, so you're pretty close.
            </div><div v-else-if="user.clicks < 6620000"><!-- 6610k - 6620k -->
              At that point you'll be removed from the leaderboard.
            </div><div v-else-if="user.clicks < 6630000"><!-- 6620k - 6630k -->
              All your effort will disappear forever.
            </div><div v-else-if="user.clicks < 6640000"><!-- 6630k - 6640k -->
              Though you do get in the Hall of Fame, so that's kind of cool.
            </div><div v-else-if="user.clicks < 6650000"><!-- 6640k - 6650k -->
              But it is fairly hidden away, nobody really sees it.
            </div><div v-else-if="user.clicks < 6660000"><!-- 6650k - 6660k -->
              Oh I guess there is one other, very minor, issue with hitting 6666666...
            </div><div v-else-if="user.clicks < 6666000"><!-- 6660k - 6666k -->
              It's probably for the best though. You need something to stop this addiction.
            </div><div v-else-if="user.clicks < 6666600"><!-- 6666k - 6666.6k -->
              And really there's only one way to get you to stop clicking...
            </div><div v-else><!-- 6,666,600 - 6,666,666 -->
              I'm going to reset your clicks.
            </div>
          </div>
          <div v-if="level < 75">
            <div v-if="userColor.Name == 'default'">
              <br>Select A Color:<br>
              <a v-for="color in colors" @click.prevent="setColor(color.Normal)" :style="{color: color.Normal}">{{color.Name}} </a>
            </div>
            <div v-else-if="level >= 50 && userColor.selected == 'Normal'">
              <br>Select A Shade:<br>
              <a @click.prevent="setColor(userColor.Light)" :style="{color: userColor.Light}">light {{userColor.Name}}</a>
              <a @click.prevent="setColor(userColor.Dark)" :style="{color: userColor.Dark}">dark {{userColor.Name}}</a>
            </div>
          </div>
        </div>
      </div>
      <div class="server">
        <div>
          <div class="ip">SERVER IP: 174.138.111.200</div>
          <div class="logintime">ON SINCE: {{ $moment(user.sessionStart, 'h:mmA M/D/Y')}}</div>
        </div>
        <div>
          <div class="session">SESSION TIME: {{ sessionTime }}</div>
          <div class="totaltime">TOTAL TIME: {{ totalTime }}</div>
        </div>
      </div>
    </template>
    <div class="login" v-else>
      <form @submit.prevent="login">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <input type="submit" value="Login">
        <input type="button" value="Register" @click.prevent="$event.isRegister = true; login($event)">
      </form>
    </div>
    <svg height="0" width="0">
      <defs>
        <clipPath v-for="i in 101" :id="'glitch' + i" clipPathUnits="objectBoundingBox">
          <rect v-for="j in glitchNumRects" :x="gdata(i,j,0)" :y="gdata(i,j,1)" :width="gsize()" :height="gsize()" />
        </clipPath>
      </defs>
    </svg>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import rng from 'seedrandom'

const glitchSeed = 1
const glitchSteps = 40 // Sets speed of animation (higher = faster). Lower results in choppyness, higher looks bad.
const glitchSize = 10.0 // % - Good compromise between speed and effect. Higher results in black frames, lower causes lag

export default {
  name: 'user',
  data: function () {
    return {
      now: new Date(),
      timer: null
    }
  },
  computed: {
    ...mapState([
      'isLoggedIn',
      'user',
      'colors'
    ]),
    ...mapGetters([
      'userColor',
      'level'
    ]),
    sessionTime: function () {
      let dur = Math.floor((this.now - new Date(this.user.sessionStart)) / 1000)
      let s = (Math.floor(dur % 60) + 100 + '').substr(1)
      let m = (Math.floor(dur / 60 % 60) + 100 + '').substr(1)
      let h = Math.floor(dur / 60 / 60 % 60) + ''
      return h + ':' + m + ':' + s
    },
    totalTime: function () {
      let dur = Math.floor((this.now - new Date(this.user.sessionStart)) / 1000) + this.user.totalTime
      let s = (Math.floor(dur % 60) + 100 + '').substr(1)
      let m = (Math.floor(dur / 60 % 60) + 100 + '').substr(1)
      let h = Math.floor(dur / 60 / 60) + ''
      return h + ':' + m + ':' + s
    },
    nextClicks: function () {
      if (this.level === 100 && this.user.hardcore === 0) return '∞'
      return this.$clicksForLevel(this.level + 1, this.user.hardcore) - this.user.clicks
    },
    showGlitch: function () {
      return this.user.clicks >= 6666600 && this.user.hardcore === 0
    },
    glitchCoverage: function () {
      return this.showGlitch ? 3 * Math.max(6666667 - this.user.clicks, 0) : 0
    },
    glitchNumRects: function () {
      let elems = 100.0 / glitchSize
      elems *= elems
      elems *= (this.glitchCoverage / 100.0)
      return Math.ceil(elems)
    },
    glitchRectData: function () {
      let rand = rng(glitchSeed)
      let data = []
      for (let i = 0; i < glitchSteps * this.glitchNumRects * 2; i++) {
        data.push(rand())
      }
      return data
    }
  },
  methods: {
    ...mapActions([
      'login',
      'setColor'
    ]),
    gdata: function (polygonNum, rectNum, attrNum) {
      polygonNum = Math.floor(polygonNum * glitchSteps / 100.0)
      return this.glitchRectData[attrNum + 2 * (rectNum + glitchSteps * polygonNum)]
    },
    gsize: function () {
      return glitchSize / 100.0
    }
  },
  created: function () {
    this.timer = setInterval(() => {
      this.now = new Date()
    }, 200)
  },
  beforeUnmount: function () {
    clearInterval(this.timer)
  }
}
</script>

<style scoped lang="scss">
.user {
  grid-area: user;
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
  margin: 8px 10px;
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

.glitch {
  animation: 2s linear infinite glitch-anim;
}

@keyframes glitch-anim {
  @for $i from 0 through 100 {
    $name: '#glitch' + ($i + 1);
    #{percentage($i / 100)}{
      clip-path: url($name);
    }
  }
}
</style>
