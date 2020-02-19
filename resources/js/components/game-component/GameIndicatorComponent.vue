<template>
  <div>
    <div :class="getIndicatorClass()" />
  </div>
</template>

<script>
export default {
  props: {
    indicatorStatus: {
      type: String,
      default: '',
    },
  },
  methods: {
    getIndicatorClass() {
      if (this.indicatorStatus === 'ready') {
        return 'indicator__circle__status_ready';
      }
      if (this.indicatorStatus === 'wait') {
        return 'indicator__circle__status_wait';
      }
      return '';
    },
  },
};
</script>
