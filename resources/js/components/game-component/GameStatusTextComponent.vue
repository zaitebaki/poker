<template>
<div class="uk-flex uk-flex-middle uk-padding">
    <div>
        <p class="uk-margin-remove">{{ statusMessage }}</p>
    </div>
    <div
        class="uk-padding-small"
        v-if="indicatorStatus === 'wait'">
        <img src="/assets/images/game/waiting.gif" alt="">
    </div>
    <div class="message-block__smile"
        v-if="isVictory !== -2">
        <img class="message-block__smile-img" :src="getPathToSmile()" alt="">
    </div>
</div>
</template>

<script>
export default {
    props: {
        statusMessage: String,
        indicatorStatus: String,
        isVictory: Number
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    mounted() {
    },
    methods: {
        getPathToSmile() {
            let randSmileNumber;
            let correctPath;
            if(this.isVictory === -1) {
                randSmileNumber = this.getRandomIntInclusive(1, 9);
                correctPath = 'gameover';
            }
            if(this.isVictory === 1) {
                randSmileNumber = this.getRandomIntInclusive(1, 7);
                correctPath = 'win';
            }
            return `/assets/images/game/smiles/${correctPath}/${randSmileNumber}.png`;
        },

        // получить случайное целое число
        // максимум и минимум включаются
        getRandomIntInclusive(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
    }
}
</script>