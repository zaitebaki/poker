<template>
<div>
    <div :class="getIndicatorClass()"></div>
</div>
</template>

<script>
export default {
    props: {
        indicatorStatus: String
    },
    methods: {
        getIndicatorClass() {
            if(this.indicatorStatus === 'ready') return 'indicator__circle__status_ready';
            if(this.indicatorStatus === 'wait') return 'indicator__circle__status_wait';
        }
    }
}
</script>