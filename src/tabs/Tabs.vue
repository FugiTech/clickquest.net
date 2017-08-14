<template>
<div class="tabs">
  <ul>
    <li v-for="(tab, idx) in tabs" :class="{ 'active': tab.isActive }">
      <a :href="'#'+tab.name" @click.prevent="selectTab(idx)">{{ tab.name }}</a>
    </li>
  </ul>
  <div>
    <slot/>
  </div>
</div>
</template>

<script>
  export default {
    data: () => ({ tabs: [] }),
    created () { this.tabs = this.$children },
    mounted () { this.selectTab(0) },
    methods: {
      selectTab (selectedIndex) {
        this.tabs.forEach((tab, idx) => {
          tab.isActive = idx === selectedIndex
        })
      }
    }
  }
</script>
