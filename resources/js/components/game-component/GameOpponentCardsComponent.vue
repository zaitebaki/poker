<template>
<div class="uk-flex uk-flex-middle opponents-card__small-padding">
    <div>
        <p class="opponents-card__header-text">Карты соперника</p>
        <p
            class="uk-text opponents-card__combination-text"
            v-if="combination">
            {{ printCombination }}
            <br>
            /{{ points }}
        </p>
    </div>
    <transition-group name="cards-list" class="uk-flex uk-margin-remove" tag="ul">
    <!-- <div class="uk-flex"> -->
        <div v-for="(card, index) in arrCards" v-bind:key="card">
            <div class="uk-margin-small-left">
                <img
                    :src="getPathToImage(index)"
                    class="card__img card__img_opponent-card_true">
            </div>
        </div>
    <!-- </div> -->
    </transition-group>
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
            },
            arrCards: this.cards
        }
    },
    computed: {
        printCombination() {
            const table = this.$parent.combinationTable;
            return table[this.combination];
        },
    },
    mounted() {
        this.sortCards();
    },
    methods: {
        sortCards() {
            // let copyCardsArr = this.arrCards.slice();
            // let obj = {};
            // copyCardsArr.forEach(function(currentValue, index) {
            //     obj[currentValue] = index;
            // });
            // this.copyCardsObj = obj;
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
            let cardCode = this.cards[index];         
            let fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.png`;
            return `/assets/images/cards/${fileCardName}`;
        }
    }
}
</script>