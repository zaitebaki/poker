<template>
<div class="uk-width-2-3@m">
    <h4>Карты соперника</h4>
    <div class="uk-flex">
        <template v-for="(card, index) in cards" >
            <div class="uk-margin-small-left" v-bind:key="index">
                <img
                    :src="getPathToImage(index)"
                    class="card__img card__img_opponent-card_true">
            </div>
        </template>
    </div>
    <div>
        <p class="uk-text" v-if="combination">{{ printCombination }}</p>
    </div>
</div>
</template>

<script>
export default {
    props: {
        cards: Array,
        combination: String,
        points: String
    },
    data() {
        return {
            suitTable: {
                'c': 'chervi',
                'b': 'bubi',
                'v': 'vini',
                'k': 'kresti',
                'j': 'joker'
            }
        }
    },
    computed: {
        printCombination() {
            const table = this.$parent.combinationTable;
            const rusCombination = table[this.combination];
            return rusCombination + ' —  ' + this.points;
        },
    },
    mounted() {
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