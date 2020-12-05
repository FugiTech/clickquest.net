<template>
<div class="tabs">
  <ul>
    <template v-for="(tab, idx) in tabs">
      <li v-if="tab.show" :class="{ 'active': tab.isActive }">
        <a :href="'#'+tab.name" @click.prevent="selectTab(idx)">{{ tab.name }}</a>
      </li>
    </template>
  </ul>
  <div>
    <slot />
  </div>
</div>
</template>

<script>
  export default {
    data: () => ({ tabs: [] }),
    mounted() {
      this.selectTab(0)
    },
    methods: {
      selectTab (selectedIndex) {
        this.tabs.forEach((tab, idx) => {
          tab.isActive = idx === selectedIndex
        })
      }
    }
  }
</script>
