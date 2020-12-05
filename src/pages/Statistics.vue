<template>
  <div class="stats">
    <section>
      <div>Users: {{ $commaify(stats.users) }}</div>
      <div>Total Clicks: {{ $commaify(stats.clicks) }}</div>
      <div>Average Clicks: {{ $commaify(stats.avgClicks) }}</div>
      <div>Average Level: {{ $commaify(stats.avgLevel) }}</div>
    </section>
    <section>
      <div v-for="(user, idx) in stats.topTen" :style="{ color: user.color }">
        #{{ idx }}: {{ user.name }} at {{ $commaify(user.clicks) }} clicks [Level {{ user.level }}]
      </div>
    </section>
    <section>
      <table>
        <tr>
          <td>Color</td>
          <td># of Players</td>
          <td>Highest Clicks</td>
          <td>Total Clicks</td>
          <td>Clicks Per User</td>
        </tr>
        <tr v-for="row in stats.colors" :style="{ background: row.color, color: 'black' }">
          <td>{{ row.name }}</td>
          <td>{{ $commaify(row.players) }}</td>
          <td>{{ $commaify(row.maxClicks) }}</td>
          <td>{{ $commaify(row.clicks) }}</td>
          <td>{{ $commaify(row.avgClicks) }}</td>
        </tr>
      </table>
    </section>
    <section>
      <h2>Hall of Fame</h2>
      <h3>For all the players who defeated Level 100 and kept going</h3>
      <div v-for="user in stats.hallOfFame" :style="{ color: user.color }">
        {{ user.name }} at {{ $commaify(user.clicks) }} clicks [Level {{ user.level }}]
      </div>
    </section>
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'statistics',
  computed: mapState([
    'stats'
  ])
}
</script>

<style scoped>
section {
  margin-top: 20px;
}

h2, h3 {
  margin: 0;
}
</style>
