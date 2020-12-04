<template>
  <div class="middle">
    <user />
    <pages />
  </div>
</template>

<script>
import User from './User'
import Pages from './Pages'

export default {
  name: 'middle',
  components: {
    User,
    Pages
  }
}
</script>

<style scoped>
.middle {
  display: flex;
  flex: auto;
}
</style>
