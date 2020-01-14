<template>
<div class="uk-width-2-3@m">
    <div class="uk-flex">
        <template v-for="(card, index) in cards" >
            <div class="uk-margin-small-left" v-bind:key="index">
                <img
                    :src="getPathToImage(index)"
                    class="card__img card__img_opponent-card_true">
            </div>
        </template>
    </div>
</div>
</template>

<script>
export default {
    props: {
        cards: Array,
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
            isAlreadeChangedCards: false,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    mounted() {
        this.$root.$on('clean:cards:classes', () => {
            this.imgElementsClasses = [false, false, false, false, false];
            this.isAlreadeChangedCards = true;
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
            if(this.isAlreadeChangedCards === false) this.normalSwitcher(index);
        },
        normalSwitcher(index) {
            this.$emit('change:active:cards:storage', index);
            let newValue = !this.imgElementsClasses[index];
            this.$set(this.imgElementsClasses, index, newValue);
        },
    }
}
</script>