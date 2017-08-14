<template>
  <div class="changelog">
    <h2>Changelog</h2>
    <h3>2.0</h3>
    <b>0.0</b>
    <ul>
      <li>Rewrote the entire site & backend in vue.js & golang respectively</li>
      <li>Slightly adjusted the leveling algorithm to emulate the rounding errors of 1.X. Earlier levels are now easier to achieve.</li>
    </ul>
    <h3>1.1</h3>
    <b>2.0</b>
    <ul>
      <li>Added a click cap, with warning messages leading up to it.</li>
      <li>---REDACTED---</li>
    </ul>
    <b>1.6</b>
    <ul>
      <li>Added a list of levels with click counts associated with them. Totally required.</li>
      <li>Finally got rid of "Meet the Team". What a terrible page.</li>
    </ul>
    <b>1.5</b>
    <ul>
      <li>Modified stats page to include people 2 ranks above or below you</li>
      <li>Modified glow-text to be more glowy</li>
      <li>Made a number bigger</li>
    </ul>
    <b>1.4</b>
    <ul>
      <li>Added the chatlog in separate tab and on a separate page</li>
      <li>Hyperlinks become clickable when posted in chat</li>
      <li>Modified stats page to include fails</li>
    </ul>
    <b>1.3</b>
    <ul>
      <li>Back-End changes to support admins screwing with clickcounts without loss of true click count.</li>
      <li>Made color changes from 75 onward in a seperate tab</li>
    </ul>
    <b>1.2</b>
    <ul>
      <li>Modified stats page. (Removed latest click (look at online users). Added color for the leaderboard. Made the Color break-down chart easier to read.)</li>
    </ul>
    <b>1.1</b>
    <ul>
      <li>Stats page now shows your ranking and LRR crew rankings</li>
    </ul>
    <b>1.0</b>
    <ul>
      <li>Added a statistics page</li>
      <li>Chat saves scroll state</li>
      <li>Log out after 10 minutes of idling</li>
      <li>Slows updates after 2 minutes of idling (and stops the session timer)</li>
      <li>Modified anti-cheat procedure to (hopefully) be less buggy and more secure</li>
      <li>Random backend aesthetical changes</li>
    </ul>
    <b>0.3</b>
    <ul>
      <li>Added support for the LRR tag</li>
      <li>Updated specific users with the new LRR tag</li>
    </ul>
    <b>0.2</b>
    <ul>
      <li>Added a list of Team members</li>
      <li>Added a Changelog</li>
    </ul>
    <b>0.1</b>
    <ul>
      <li>Added Unicode support to chat</li>
      <li>Updated Alex's Twitter link</li>
      <li>Made website links open in a new window</li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'changelog'
}
</script>

<style scoped>
h2, h3 {
	margin-top: 0px;
	margin-bottom: 0px;
}
b {
  margin-left: 36px;
}
ul {
  padding-left: 20px;
}
</style>
