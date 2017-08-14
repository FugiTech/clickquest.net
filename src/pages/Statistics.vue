<template>
  <div class="stats">
    <section>
      <div>Users: {{ stats.users | commaify }}</div>
      <div>Total Clicks: {{ stats.clicks | commaify }}</div>
      <div>Average Clicks: {{ stats.avgClicks | commaify }}</div>
      <div>Average Level: {{ stats.avgLevel | commaify }}</div>
    </section>
    <section>
      <div v-for="(user, idx) in stats.topTen" :style="{ color: user.color }">
        #{{ idx }}: {{ user.name }} at {{ user.clicks | commaify }} clicks [Level {{ user.level }}]
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
          <td>{{ row.players | commaify }}</td>
          <td>{{ row.maxClicks | commaify }}</td>
          <td>{{ row.clicks | commaify }}</td>
          <td>{{ row.avgClicks | commaify }}</td>
        </tr>
      </table>
    </section>
    <section>
      <h2>Hall of Fame</h2>
      <h3>For all the players who defeated Level 100 and kept going</h3>
      <div v-for="user in stats.hallOfFame" :style="{ color: user.color }">
        {{ user.name }} at {{ user.clicks | commaify }} clicks [Level {{ user.level }}]
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
  ]),
  filters: {
    commaify: (value) => {
      return value ? value.toLocaleString('en-US') : ''
    }
  }
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
