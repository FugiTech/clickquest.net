<template>
  <div class="colorSelect">
    <div v-for="color in colors">
      <div @click.prevent="setColor(userColor.Light)" :style="{color: color.Light}">light {{color.Name}}</div>
      <div @click.prevent="setColor(userColor.Normal)" :style="{color: color.Normal}">{{color.Name}}</div>
      <div @click.prevent="setColor(userColor.Dark)" :style="{color: color.Dark}">dark {{color.Name}}</div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'colorList',
  computed: mapState([
    'colors'
  ]),
  methods: mapActions([
    'setColor'
  ])
}
</script>

<style scoped>
.colorSelect {
  display: flex;
  flex-wrap: wrap;
}
.colorSelect > div {
  margin: 10px;
  font-size: 1.4em;
}
</style>
