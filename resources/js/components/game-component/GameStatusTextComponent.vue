<template>
<div class="uk-flex uk-flex-middle uk-padding">
    <div>
        <p class="uk-margin-remove">{{ statusMessage }}</p>
    </div>
    <div
    class="uk-padding-small"
    v-if="indicatorStatus === 'wait'">
        <img src="/assets/images/game/830.gif" alt="">
    </div>
</div>
</template>

<script>
export default {
    props: {
        statusMessage: String,
        indicatorStatus: String,
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
}
</script>