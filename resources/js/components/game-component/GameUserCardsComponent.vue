<template>
<div class="uk-flex">
    <template v-for="(card, index) in cards" >
        <div class="uk-margin-small-left" v-bind:key="index">
            <img
                :src="getPathToImage(index)"
                class="card__img"
                v-bind:class="{'card__img_change': imgElementsClasses[index]}"
                v-on:click="switcher(index)"
                alt="">
        </div>
    </template>
</div>
</template>

<script>
export default {
    props: {
        cards: Array,
        // formRoute: String,
        // formButtonCaption: String
    },
    data() {
        return {
            suitTable: {
                'c': 'chervi',
                'b': 'bubi',
                'v': 'vini',
                'k': 'kresti',
                'j': 'joker'
            },
            changeThisCard: false,
            imgElementsClasses: [false, false, false, false, false],
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    mounted() {
        // this.imgElementsClasses = new Array(5).fill('false');
        console.log(this.imgElementsClasses);
    },
    methods: {
        getPathToImage(index) {
            let cardCode = this.cards[index];         
            let fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.jpg`;
            return `/assets/images/cards/${fileCardName}`;
        },
        switcher(index) {
            let newValue = !this.imgElementsClasses[index]; 
            this.$set(this.imgElementsClasses, index, newValue);
        }
    }
}
</script>