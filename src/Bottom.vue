<template>
  <div class="bottom">
    <h1>Online Players ({{ players.length }})</h1>
    <div class="players">
      <!-- Placeholder for sizing -->
      <div style="width: 0; margin: 0"><p class="name">&nbsp;</p><p class="level">&nbsp;</p></div>

      <div v-for="player in players" :style="{color: player.color}">
        <p class="name">{{ player.hardcore ? '[H]' : '' }}{{ player.name }}</p>
        <p class="level">{{ $levelForClicks(player.clicks, player.hardcore) }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'bottom',
  computed: mapState([
    'players'
  ]),
}
</script>

<style scoped>
.bottom {
  grid-area: bottom;
  display: flex;
  flex-direction: column;
  margin: 15px;
  text-align: center;
}

.players {
  display: flex;
  overflow-x: auto;
}

.players > div {
  margin-right: 20px;
}

.name {
  margin: 0;
  font-size: 1.5em;
}

.level {
  margin: 0;
  font-size: 4em;
}
</style>
