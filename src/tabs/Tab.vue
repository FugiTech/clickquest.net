<template>
  <section v-if="show" v-show="isActive">
    <slot />
  </section>
</template>

<script>
  export default {
    props: {
      name: { required: true },
      show: { type: Boolean, default: true }
    },
    data: () => ({
      isActive: false,
      reset: () => {
        this.$parent.selectTab(0)
      }
    }),
    created() {
      this.$parent.tabs.push(this)
    },
    watch: {
      show: function () {
        if (!this.show && this.isActive) this.reset()
      }
    }
  }
</script>
