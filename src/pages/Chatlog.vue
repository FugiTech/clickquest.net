<template>
  <div class="chatlog">
    <div class="logctrl">
      <span class="info">no log</span><br/>
      <input type="button" id="prevlog" value="Previous"/>
      <input type="text" class="pid" value="1" size="5"/>
      <input type="button" id="golog" value="Go"/>
      <input type="button" id="nextlog" value="Next"/>
      <a href="chatlog.php" target="_blank">Seperate Chatlog</a>
    </div>
    <div id="logdata" class="content"></div>
  </div>
</template>

<script>
export default {
  name: 'chatlog'
}
</script>

<style scoped>
</style>
