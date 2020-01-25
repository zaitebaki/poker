<template>
<div class="uk-flex uk-flex-center uk-margin-small-top">
    <div>
        <p class="uk-text" v-if="combination">{{ printCombination }}</p>
    </div>
    <transition-group name="cards-list" class="uk-flex" tag="ul" v-if="arrCards">
        <div v-for="(card, index) in getArrCards" v-bind:key="card">
            <div class="uk-margin-small-left">
                <img
                    :src="getPathToImage(index)"
                    class="card__img"
                    v-bind:class="{'card__img_change': imgElementsClasses[index]}"
                    v-on:click="switcher(card, index)"
                    alt="">
            </div>
        </div>
    </transition-group>
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
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            copyCardsObj: [],
            arrCards: this.cards
        }
    },
    computed: {
        printCombination() {
            const table = this.$parent.combinationTable;
            const rusCombination = table[this.combination];
            return rusCombination + ' —  ' + this.points;
        },
        getArrCards() {
            return this.arrCards;
        }
    },
    mounted() {
        this.sortCards();

        this.$root.$on('clean:cards:classes', () => {
            this.imgElementsClasses = [false, false, false, false, false];
            this.changedCardsFlag = true;
            setTimeout(() => {
                this.arrCards = this.cards;
                this.sortCards();
            }, 100);
        });

        this.$root.$on('changed:cards:false', () => {
            this.imgElementsClasses = [false, false, false, false, false];
            this.changedCardsFlag = false;
            setTimeout(() => {
                this.arrCards = this.cards;
                this.sortCards();
            }, 100);
        });

        this.handleSwitcher = this.normalSwitcher;
    },
    methods: {
        sortCards() {
            let copyCardsArr = this.arrCards.slice();
            let obj = {};
            copyCardsArr.forEach(function(currentValue, index) {
                obj[currentValue] = index;
            });
            this.copyCardsObj = obj;
            this.arrCards.sort(this.compareCards);
        },
        compareCards(a, b) {

            const valueA = this.getValueForSmb(a);
            const valueB = this.getValueForSmb(b);

            if (valueA > valueB) return 1;
            if (valueA == valueB) return 0;
            if (valueA < valueB) return -1;
        },
        getValueForSmb(smb) {
            const smbTable = {
                'x': 10,
                'v': 11,
                'd': 12,
                'k': 13,
                't': 14,
            };
            if(smb === '1j' || smb === '2j')
                return 20;

            const curSmb = smb[0];
            if(curSmb in smbTable) {
                return smbTable[curSmb];
            }
            else {
                return Number.parseInt(curSmb);
            }  
        },
        getPathToImage(index) {
            const cardCode = this.arrCards[index];
            let fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.png`;
            return `/assets/images/cards/${fileCardName}`;
        },
        switcher(card, index) {
            if(this.changedCardsFlag === false) this.normalSwitcher(card, index);
        },
        normalSwitcher(card, index) {
            const srcIndex = this.copyCardsObj[card];
            console.log(srcIndex);
            this.$emit('change:active:cards:storage', srcIndex);
            let newValue = !this.imgElementsClasses[index];
            this.$set(this.imgElementsClasses, index, newValue);
        },
    }
}
</script>