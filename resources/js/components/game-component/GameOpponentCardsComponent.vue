<template>
  <div class="uk-padding-small uk-padding-remove-left uk-padding-remove-right">
    <div
      class="uk-hidden@s"
    >
      <div>
        <p class="opponents-card__header-text opponents-card__mobile_text">
          Карты соперника
        </p>
        <p
          v-if="combination"
          class="uk-text opponents-card__combination-text opponents-card__mobile-combination"
        >
          {{ printCombination }}
          / {{ points }}
        </p>
      </div>
      <div>
        <transition-group
          name="cards-list"
          class="uk-flex uk-margin-remove uk-padding-remove uk-flex-center"
          tag="ul"
        >
          <div
            v-for="(card, index) in arrCards"
            :key="card"
          >
            <div
              :class="{'gocc__card': index !== 0}"
            >
              <img
                :src="getPathToImage(index)"
                class="card__img card__img_opponent-card_true"
              >
            </div>
          </div>
        </transition-group>
      </div>
    </div>

    <div
      uk-grid
      class="uk-visible@s"
    >
      <div>
        <p class="opponents-card__header-text opponents-card__mobile_text">
          Карты соперника
        </p>
        <p
          v-if="combination"
          class="uk-text opponents-card__combination-text opponents-card__mobile-combination"
        >
          {{ printCombination }}
          / {{ points }}
        </p>
      </div>
      <div>
        <transition-group
          name="cards-list"
          class="uk-flex"
          tag="ul"
        >
          <div
            v-for="(card, index) in arrCards"
            :key="card"
          >
            <div
              :class="{'gocc__card': index !== 0}"
            >
              <img
                :src="getPathToImage(index)"
                class="card__img card__img_opponent-card_true"
              >
            </div>
          </div>
        </transition-group>
      </div>
    </div>
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
      arrCards: this.cards,
    };
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
      const cardCode = this.cards[index];
      const fileCardName = `${this.suitTable[cardCode[1]]}-${cardCode[0]}.png`;
      return `/assets/images/cards/${fileCardName}`;
    },
  },
};
</script>
<style scoped>
  .gocc__card {
    margin-left: 10px;
  }
  @media (max-width: 640px) {
    .opponents-card__mobile_text {
      padding: 0 0 0 15px;
      margin: 0;
    }
    .opponents-card__mobile-combination {
      padding: 0 0 10px 15px;
      margin: 0;
    }
    .opponents-card__header-text {
      font-size: 14px;
    }
    .gocc__card {
      margin-left: 5px;
    }
    .opponents-card__combination-text {
      font-size: 16px;
    }
    .fefefe {
      /* margin-left: 10px; */
    }
  }
</style>


