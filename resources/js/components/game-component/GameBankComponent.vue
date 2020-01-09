<template>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-4@m">
        <!-- <h3 class="uk-card-title">{{ content.header }}</h3> -->
        <p><img src="/assets/images/game/money-icon.svg" alt="" class="money__img">
        <span>{{ money }} руб.</span>
        </p>
    </div>
</template>

<script>
export default {
    props: {
        money: String,
    },
    // data() {
    //     return {
    //         csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //     }
    // }
}
</script>