<template>
  <div class="levelList">
    <div>
      <h1>Normal</h1>
      <span v-for="i in 100">
        Level {{ i }}: {{ $clicksForLevel(i, false) }} clicks [+{{ $increaseForLevel(i, false) }}]
      </span>
    </div>
    <div>
      <h1>Hardcore</h1>
      <span v-for="i in 100">
        Level {{ i }}: {{ $clicksForLevel(i, true) }} clicks [+{{ $increaseForLevel(i, true) }}]
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'levelList',
}
</script>

<style scoped>
.levelList {
  display: flex;
}

.levelList > div {
  flex: 50%;
}

span {
  display: block;
}
</style>
