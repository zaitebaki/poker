<template>
  <div
    class="uk-margin-medium-top uk-margin-medium-bottom"
    :class="{'uk-margin-remove uk-padding-small': isActiveButton('then')}"
  >
    <div class="uk-flex uk-flex-center">
      <p
        uk-margin
        class="uk-margin-remove"
      >
        <button
          v-if="isActiveButton('startGame')"
          class="uk-button uk-button-primary"
          :disabled="startButtonIndicator"
          @click="startGame()"
        >
          {{ buttonsCaptions.startButton }}
        </button>
        <button
          v-if="isActiveButton('changeCards')"
          class="uk-button uk-button-secondary"
          :disabled="indicatorStatus === 'wait'"
          @click="changeCards('change')"
        >
          {{ buttonsCaptions.changeCards }}
        </button>
        <button
          v-if="isActiveButton('notChange')"
          class="uk-button uk-button-danger"
          :disabled="indicatorStatus === 'wait'"
          @click="changeCards('no:change')"
        >
          {{ buttonsCaptions.notChange }}
        </button>
        <button
          v-if="isActiveButton('addMoney')"
          class="uk-button uk-button-primary"
          :disabled="indicatorStatus === 'wait'"
          @click="addMoney()"
        >
          {{ addMoneyCaption }}
        </button>
        <button
          v-if="isActiveButton('noMoney')"
          class="uk-button uk-button-secondary"
          :disabled="indicatorStatus === 'wait'"
          @click="check()"
        >
          {{ buttonsCaptions.noMoney }}
        </button>
        <button
          v-if="isActiveButton('equalAndAdd')"
          class="uk-button uk-button-primary"
          :disabled="indicatorStatus === 'wait'"
          @click="equalAndAdd()"
        >
          {{ equalAndAddCaption }}
        </button>
        <button
          v-if="isActiveButton('equal')"
          class="uk-button uk-button-primary"
          :disabled="indicatorStatus === 'wait'"
          @click="equal()"
        >
          {{ equalCaption }}
        </button>
        <button
          v-if="isActiveButton('gameOver')"
          class="uk-button uk-button-danger"
          :disabled="indicatorStatus === 'wait'"
          @click="gameOver()"
        >
          {{ buttonsCaptions.gameOver }}
        </button>
        <button
          v-if="isActiveButton('then')"
          class="uk-button uk-button-danger"
          :disabled="newGameButtonIndicator"
          @click="then()"
        >
          {{ buttonsCaptions.then }}
        </button>
      </p>
    </div>
    <div
      v-if="!isActiveButton('then')"
      class="uk-flex uk-flex-center uk-margin-small-top"
    >
      <b-form-slider
        v-if="isActiveButton('addMoney') || isActiveButton('equalAndAdd')"
        ref="ticks"
        v-model="moneySumForAdd"
        :step="5"
        :min="5"
        :max="100"
        :ticks="ticks"
        tooltip="hide"
        :ticks-labels="tickLabels"
      />
    </div>
  </div>
</template>

<script>
import bFormSlider from 'vue-bootstrap-slider/es/form-slider';

export default {
  components: {
    'b-form-slider': bFormSlider,
  },
  props: {
    roomUrl: {
      type: String,
      required: true,
    },
    roomName: {
      type: String,
      default: '',
    },
    buttonsCaptions: {
      type: Object,
      required: true,
    },
    buttons: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      required: true,
    },
    activeCardsStorage: {
      type: Array,
      default: () => [],
    },
    indicatorStatus: {
      type: String,
      default: '',
    },
    money: {
      type: String,
      default: '',
    },
    addOpponentMoney: {
      type: String,
      default: '',
    },
    increaseAfterEqualMoney: {
      type: String,
      default: '',
    },
    startButtonIndicator: Boolean,
    opponentStatusCheck: Boolean,
    newGameButtonIndicator: Boolean,
  },
  data() {
    return {
      moneySumForAdd: 5,
      ticks: [5, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
      tickLabels: [
        '5',
        '10',
        '20',
        '30',
        '40',
        '50',
        '60',
        '70',
        '80',
        '90',
        '100',
      ],
      // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
  },
  computed: {
    addMoneyCaption() {
      const re = /:money/gi;
      return this.buttonsCaptions.addMoney.replace(re, this.moneySumForAdd);
    },
    equalAndAddCaption() {
      let re = /:money1/gi;
      const caption = this.buttonsCaptions.equalAndAdd.replace(
        re,
        this.getCurrentAddingMoney()
      );
      re = /:money2/gi;
      return caption.replace(re, this.moneySumForAdd);
    },
    equalCaption() {
      const re = /:money/gi;
      return this.buttonsCaptions.equal.replace(
        re,
        this.getCurrentAddingMoney()
      );
    },
  },
  mounted() {},

  methods: {
    // инициировать раздачу карт
    startGame() {
      axios
        .post(this.roomUrl, {initAction: 'startGame', roomName: this.roomName})
        .then(response => {
          this.$emit('update:parameters', response.data);
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // поменять карты
    changeCards(change) {
      if (change !== 'no:change' && this.isNotChoosingCardsForChanging()) {
        return;
      }
      axios
        .post(this.roomUrl, {
          initAction: 'changeCards',
          roomName: this.roomName,
          cardsIndexForChange: this.getcardsIndexForChange(change),
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
          this.$root.$emit('clean:cards:classes');
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // добавить карты
    addMoney() {
      axios
        .post(this.roomUrl, {
          initAction: 'addMoney',
          roomName: this.roomName,
          money: this.moneySumForAdd,
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // чек
    check() {
      // если оппонент уже ответил "чек"
      if (this.opponentStatusCheck === true) {
        axios
          .post(this.roomUrl, {
            initAction: 'opponentCheck',
            roomName: this.roomName,
          })
          .then(response => {
            this.$emit('update:parameters', response.data);
          })
          .catch(error => {
            this.$emit('catch:error', error);
          });
        // пользователь говорит "чек" в 1-ый раз
      } else {
        axios
          .post(this.roomUrl, {
            initAction: 'check',
            roomName: this.roomName,
          })
          .then(response => {
            this.$emit('update:parameters', response.data);
          })
          .catch(error => {
            this.$emit('catch:error', error);
          });
      }
    },

    // сравянть и добавить
    equalAndAdd() {
      axios
        .post(this.roomUrl, {
          initAction: 'equalAndAdd',
          roomName: this.roomName,
          moneyequal: this.getCurrentAddingMoney(),
          moneyAdd: this.moneySumForAdd,
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // сравнять
    equal() {
      axios
        .post(this.roomUrl, {
          initAction: 'equal',
          roomName: this.roomName,
          money: this.getCurrentAddingMoney(),
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // сбросить карты
    gameOver() {
      axios
        .post(this.roomUrl, {
          initAction: 'gameOver',
          roomName: this.roomName,
          money: this.getDropMoney(),
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // начать новую партию
    then() {
      axios
        .post(this.roomUrl, {
          initAction: 'nextRound',
          roomName: this.roomName,
        })
        .then(response => {
          this.$emit('update:parameters', response.data);
          this.$root.$emit('changed:cards:false');
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },

    // проверить есть ли карты для замены
    isNotChoosingCardsForChanging() {
      for (let i = 0; i < this.activeCardsStorage.length; ++i) {
        if (this.activeCardsStorage[i] === false) return false;
      }
      return true;
    },

    // вернуть индекс карты для замены
    getcardsIndexForChange(change) {
      if (change === 'no:change') return false;
      const indexArray = [];
      this.activeCardsStorage.forEach((item, i) => {
        if (item === false) {
          indexArray.push(i);
        }
      });

      if (indexArray.length === 0) return false;
      return indexArray.join(',');
    },

    isActiveButton(nameButton) {
      if (this.buttons && this.buttons.indexOf(nameButton) !== -1) {
        return true;
      }
      return false;
    },

    getCurrentAddingMoney() {
      if (this.increaseAfterEqualMoney !== '0') {
        return this.increaseAfterEqualMoney;
      }

      return this.addOpponentMoney;
    },
    getDropMoney() {
      return (+this.money - +this.getCurrentAddingMoney()) / 2;
    },
  },
};
</script>
