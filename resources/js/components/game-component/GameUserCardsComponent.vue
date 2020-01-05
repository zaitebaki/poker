<template>
<div class="uk-flex">
    <template v-for="(card, index) in cards">
        <div class="uk-margin-small-left" v-bind:key="index">
            <img :src="getPathToImage(index)" class="card__img" alt="">
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
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    mounted() {
        console.log('efefef');
        console.log(this.cards);
    },
    methods: {
        getPathToImage(index) {
            let cardCode = this.cards[index];         
            let fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.jpg`;
            return `/assets/images/cards/${fileCardName}`;
        }
    }
}
</script>