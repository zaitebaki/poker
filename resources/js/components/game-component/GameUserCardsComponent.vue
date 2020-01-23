<template>
<div class="uk-flex uk-flex-center uk-margin-small-top">
    <div>
        <p class="uk-text" v-if="combination">{{ printCombination }}</p>
    </div>
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
</div>
</template>

<script>
export default {
    props: {
        cards: Array,
        combination: String,
        points: String,
        isAlreadyChangedCards: Boolean
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
            imgElementsClasses: [false, false, false, false, false],
            changedCardsFlag: this.isAlreadyChangedCards,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        this.$root.$on('clean:cards:classes', () => {
            this.imgElementsClasses = [false, false, false, false, false];
            this.changedCardsFlag = true;
        });

        this.$root.$on('changed:cards:false', () => {
            this.imgElementsClasses = [false, false, false, false, false];
            this.changedCardsFlag = false;
        });

        this.handleSwitcher = this.normalSwitcher;
    },
    methods: {
        getPathToImage(index) {
            let cardCode = this.cards[index];
            let fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.jpg`;
            return `/assets/images/cards/${fileCardName}`;
        },
        switcher(index) {
            console.log(this.changedCardsFlag);
            console.log(this.isAlreadyChangedCards);
            if(this.changedCardsFlag === false) this.normalSwitcher(index);
        },
        normalSwitcher(index) {
            this.$emit('change:active:cards:storage', index);
            let newValue = !this.imgElementsClasses[index];
            this.$set(this.imgElementsClasses, index, newValue);
        },
    }
}
</script>