<template>
  <div>
    <div>
      <p
        v-if="combination"
        class="uk-text opponents-card__combination-text opponents-card__mobile-text"
      >
        {{ printCombination }}
        /{{ points }}
      </p>
    </div>
    <transition-group
      v-if="arrCards"
      name="cards-list"
      class="uk-flex uk-margin-remove uk-padding-remove uk-flex-center"
      tag="ul"
    >
      <div
        v-for="(card, index) in getArrCards"
        :key="card"
      >
        <div
          :class="{'gucc__card': index !== 0}"
        >
          <img
            :src="getPathToImage(index)"
            class="card__img"
            :class="{card__img_change: imgElementsClasses[index]}"
            alt=""
            @click="switcher(card, index)"
          >
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script>
export default {
  props: {
    cards: {
      type: Array,
      default: () => [],
    },
    combination: {
      type: String,
      default: '',
    },
    points: {
      type: String,
      default: '',
    },
    isAlreadyChangedCards: Boolean,
  },
  data() {
    return {
      suitTable: {
        c: 'chervi',
        b: 'bubi',
        v: 'vini',
        k: 'kresti',
        j: 'joker',
      },
      imgElementsClasses: [false, false, false, false, false],
      changedCardsFlag: this.isAlreadyChangedCards,
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content'),
      copyCardsObj: [],
      arrCards: this.cards,
    };
  },
  computed: {
    printCombination() {
      const table = this.$parent.combinationTable;
      return table[this.combination];
    },
    getArrCards() {
      return this.arrCards;
    },
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
      const copyCardsArr = this.arrCards.slice();
      const obj = {};
      copyCardsArr.forEach((currentValue, index) => {
        obj[currentValue] = index;
      });
      this.copyCardsObj = obj;
      this.arrCards.sort(this.compareCards);
    },
    compareCards(a, b) {
      const valueA = this.getValueForSmb(a);
      const valueB = this.getValueForSmb(b);

      if (valueA > valueB) return 1;
      if (valueA === valueB) return 0;
      return -1;
    },
    getValueForSmb(smb) {
      const smbTable = {
        x: 10,
        v: 11,
        d: 12,
        k: 13,
        t: 14,
      };
      if (smb === '1j' || smb === '2j') return 20;

      const curSmb = smb[0];
      if (curSmb in smbTable) {
        return smbTable[curSmb];
      }

      return Number.parseInt(curSmb, 10);
    },
    getPathToImage(index) {
      const cardCode = this.arrCards[index];
      const fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.png`;
      return `/assets/images/cards/${fileCardName}`;
    },
    switcher(card, index) {
      if (this.changedCardsFlag === false) this.normalSwitcher(card, index);
    },
    normalSwitcher(card, index) {
      const srcIndex = this.copyCardsObj[card];
      this.$emit('change:active:cards:storage', srcIndex);
      const newValue = !this.imgElementsClasses[index];
      this.$set(this.imgElementsClasses, index, newValue);
    },
  },
};
</script>
<style scoped>
  .gucc__card {
    margin-left: 10px;
  }
  @media (max-width: 640px) {
    .gucc__card {
      margin-left: 5px;
    }
    .opponents-card__combination-text {
      font-size: 16px;
    }
    .opponents-card__mobile-text {
      padding-left: 15px;
    }
  }
</style>
