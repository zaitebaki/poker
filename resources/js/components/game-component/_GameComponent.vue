<template>
  <div class="uk-container cg__padding_none">
    <game-status-bar-component
      :content="content.header"
      :user="userParameters"
      :opponent-money="vueGameParameters.withOpponentMoney"
      :room-id="gameParameters.roomId"
      :room-name="roomName"
      @catch:error="handleError(error)"
    />

    <div class="uk-grid">
      <game-bank-component
        :money="vueGameParameters.money"
        :bank-messages="vueGameParameters.bankMessages"
      />

      <div
        class="uk-width-expand uk-flex uk-flex-middle"
        :class="getBackgroundColorClass"
      >
        <game-status-text-component
          :status-message="vueGameParameters.statusMessage"
          :indicator-status="vueGameParameters.indicator"
          :is-victory="getVictoryStatus"
        />
      </div>
    </div>
    
    <game-opponent-cards-component
      v-if="vueGameParameters.opponentUserCards"
      :cards="vueGameParameters.opponentUserCards"
      :combination="vueGameParameters.opponentCombination"
      :points="vueGameParameters.opponentPoints"
    />
    
    <game-button-panel-component
      :room-url="roomUrl"
      :room-name="roomName"
      :buttons-captions="content.buttonsCaptions"
      :buttons="vueGameParameters.buttons"
      :user="userParameters"
      :active-cards-storage="activeCardsStorage"
      :indicator-status="vueGameParameters.indicator"
      :money="vueGameParameters.money"
      :add-opponent-money="vueGameParameters.addOpponentMoney"
      :increase-after-equal-money="vueGameParameters.increaseAfterEqualMoney"
      :opponent-status-check="vueGameParameters.opponentStatusCheck"
      :start-button-indicator="vueGameParameters.startButtonIndicator"
      :new-game-button-indicator="vueGameParameters.newGameButtonIndicator"
      @update:parameters="updateParameters($event)"
      @catch:error="handleError(error)"
    />

    <game-user-cards-component
      v-if="vueGameParameters.userCards"
      :cards="vueGameParameters.userCards"
      :combination="vueGameParameters.userCombination"
      :points="vueGameParameters.userPoints"
      :is-already-changed-cards="vueGameParameters.isAlreadyChangedCards"
      @change:active:cards:storage="changeActiveCardsStorage($event)"
    />

    <mobile-menu-component
      :room-id="gameParameters.roomId"
      class="uk-hidden@s"
    />

    <!-- модальное окно о завершении сеанса игры -->
    <div
      id="js-modal-dialog"
      uk-modal
    >
      <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title" />
        <p class="uk-text-center uk-text-danger">
          {{ getFinishGameMessage }}
        </p>
        <button
          class="uk-modal-close-outside"
          type="button"
          uk-close
        />
        <p class="uk-text-center">
          <button
            class="uk-button uk-button-danger uk-modal-close"
            type="button"
            @click="finishGameButtonClick()"
          >
            ok
          </button>
        </p>
      </div>
    </div>

    <!-- модальное окно об ошибке -->
    <div
      id="js-modal-error"
      uk-modal
    >
      <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title" />
        <p class="uk-text-center uk-text-danger">
          {{ content.errors.mainMessage }}
        </p>
        <button
          class="uk-modal-close-outside"
          type="button"
          uk-close
        />
        <p class="uk-text-center">
          <button
            class="uk-button uk-button-danger uk-modal-close"
            type="button"
          >
            ok
          </button>
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import GameStatusBarComponent from './GameStatusBarComponent.vue';
import GameBankComponent from './GameBankComponent.vue';
import GameButtonPanelComponent from './GameButtonPanelComponent.vue';
import GameStatusTextComponent from './GameStatusTextComponent.vue';
import GameUserCardsComponent from './GameUserCardsComponent.vue';
import GameOpponentCardsComponent from './GameOpponentCardsComponent.vue';
import MobileMenuComponent from './MobileMenuComponent.vue';

export default {
  components: {
    'game-status-bar-component': GameStatusBarComponent,
    'game-bank-component': GameBankComponent,
    'game-button-panel-component': GameButtonPanelComponent,
    'game-status-text-component': GameStatusTextComponent,
    'game-user-cards-component': GameUserCardsComponent,
    'game-opponent-cards-component': GameOpponentCardsComponent,
    'mobile-menu-component': MobileMenuComponent,
  },
  props: {
    content: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    gameParameters: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      vueGameParameters: this.gameParameters,
      userParameters: this.user,
      activeCardsStorage: [true, true, true, true, true],
      combinationTable: {
        POKER: 'Покер',
        STREETFLASH: 'Стрит флеш',
        KARE: 'Каре',
        FULLHOUSE: 'Фулхауз',
        FLASH: 'Флеш',
        STREET: 'Стрит',
        TROIKA: 'Тройка',
        TWO_PAIRS: 'Две пары',
        DVOIKA: 'Двойка',
        WASTE: 'Хлам',
      },
      roomUrl: `/game/room/${this.gameParameters.roomId}`,
      roomName: `room_${this.gameParameters.roomId}`,
      isFinishGameComponentVisible: false,
      opponentUserName: '',
    };
  },
  computed: {
    gameActionChannel() {
      return window.Echo.private(
        `room-action.${this.vueGameParameters.roomId}`
      );
    },
    getVictoryStatus() {
      if ('isVictory' in this.vueGameParameters) {
        return this.vueGameParameters.isVictory;
      }
      return -2;
    },
    getBackgroundColorClass() {
      if (this.vueGameParameters.isVictory === -1) {
        return 'status-text__background_color_red';
      }
      return this.vueGameParameters.indicator === 'wait'
        ? 'status-text__background_color_orange'
        : 'status-text__background_color_green';
    },
    getFinishGameMessage() {
      const re = /:user/gi;
      return this.content.alertMessages.finishGameSessionMessage.replace(
        re,
        this.opponentUserName
      );
    },
  },
  mounted() {
    this.$root.$on('changed:cards:false', () => {
      this.activeCardsStorage = [true, true, true, true, true];
    });
    this.gameActionChannel
      .listen('SendReadyStatus', () => {
        axios
          .post(this.roomUrl, {
            updateState: 'ReadyState',
            roomName: this.roomName,
            sendPost: 'true',
          })
          .then(response => {
            this.vueGameParameters = response.data.gameParameters;
          })
          .catch(error => {
            this.handleError(error);
          });
        this.startGameButtonReady = true;
      })
      .listen('SendStartChangeCardsStatus', () => {
        axios
          .post(this.roomUrl, {
            updateState: 'StartedGameState',
            roomName: this.roomName,
          })
          .then(response => {
            this.vueGameParameters = response.data.gameParameters;
          })
          .catch(error => {
            this.handleError(error);
          });
        this.startGameButtonReady = true;
      })
      .listen('SendStartedGameStatus', () => {
        axios
          .post(this.roomUrl, {
            initAction: 'startGame',
            roomName: this.roomName,
          })
          .then(response => {
            this.vueGameParameters = response.data.gameParameters;
            this.$root.$emit('changed:cards:false');
          })
          .catch(error => {
            this.handleError(error);
          });
      })
      .listen('SendBettingStatus', () => {
        axios
          .post(this.roomUrl, {
            updateState: 'StartedGameState',
            roomName: this.roomName,
          })
          .then(response => {
            this.vueGameParameters = response.data.gameParameters;
          })
          .catch(error => {
            this.handleError(error);
          });
      })
      .listen('SendFinishChangeStatus', () => {
        axios
          .post(this.roomUrl, {
            updateState: 'BettingState',
            roomName: this.roomName,
            correctionStatusMessage: 'changeFinished',
          })
          .then(response => {
            this.vueGameParameters = response.data.gameParameters;
          })
          .catch(error => {
            this.handleError(error);
          });
      })
      .listen('SendFinishBettingStatus', ({money, moneyIncrease}) => {
        let moneyIncreaseValue = moneyIncrease;
        const moneyValue = money;
        if (moneyIncrease === 'drop' || moneyIncrease === 'opponentCheck') {
          this.sendFinishGameRequest();
        } else {
          let correctionMessage;
          if (moneyIncrease !== '0' && moneyIncrease !== 'equal') {
            correctionMessage = 'equalAndAdd';
          } else if (money === '0' && moneyIncrease === '0') {
            correctionMessage = 'check';
          } else if (money !== '0' && moneyIncrease === 'equal') {
            moneyIncreaseValue = '';
            correctionMessage = 'equal';
          } else if (money !== '0' && moneyIncrease === 'drop') {
            moneyIncreaseValue = '';
            correctionMessage = 'drop';
          } else {
            correctionMessage = 'betFinished';
          }
          axios
            .post(this.roomUrl, {
              updateState: 'BettingState',
              roomName: this.roomName,
              correctionStatusMessage: correctionMessage,
              money: moneyValue,
              moneyIncrease: moneyIncreaseValue,
            })
            .then(response => {
              this.vueGameParameters = response.data.gameParameters;
              if (
                correctionMessage === 'equal' ||
                correctionMessage === 'drop'
              ) {
                this.sendFinishGameRequest();
              }
            })
            .catch(error => {
              this.handleError(error);
            });
        }
      })
      .listen('SendUpdateIndicatorButtonStatus', () => {
        this.vueGameParameters.newGameButtonIndicator = false;
      })
      .listen('SendUpdateIndicatorStartButtonStatus', () => {
        this.vueGameParameters.startButtonIndicator = false;
      })
      .listen('SendFinishGameSessionStatus', ({opponentUserName}) => {
        this.opponentUserName = opponentUserName;
        UIkit.modal('#js-modal-dialog').show();
      });
    UIkit.util.on('#js-modal-dialog', 'click', e => {
      e.preventDefault();
      e.target.blur();
      window.location.replace('/home');
    });
  },
  methods: {
    updateParameters($event) {
      if ('user' in $event) {
        this.userParameters = $event.user;
      }
      if ('gameParameters' in $event) {
        this.vueGameParameters = $event.gameParameters;
      }
    },
    changeActiveCardsStorage($event) {
      const index = $event;
      this.activeCardsStorage[index] = !this.activeCardsStorage[index];
    },
    sendFinishGameRequest() {
      axios
        .post(this.roomUrl, {
          updateState: 'FinishState',
          roomName: this.roomName,
        })
        .then(response => {
          this.vueGameParameters = response.data.gameParameters;
          this.userParameters = response.data.user;
          // сделать кнопку "продолжить" доступной у оппонента
          this.sendResponseAfterFinishEvent();
        })
        .catch(error => {
          this.handleError(error);
        });
    },
    getCardsArr() {
      const array = [];
      if (
        'userCards' in this.vueGameParameters &&
        this.vueGameParameters.userCards
      ) {
        this.vueGameParameters.userCards.forEach((item, i) => {
          const obj = {
            index: i,
            value: item,
          };
          array.push(obj);
        });
      }
      return array;
    },
    sendResponseAfterFinishEvent() {
      axios
        .post(this.roomUrl, {
          initAction: 'delNewGameButtonIndicator',
          roomName: this.roomName,
        })
        .then(() => {})
        .catch(error => {
          this.handleError(error);
        });
    },
    finishGameButtonClick() {
      window.location.replace('/home');
    },
    // обработать ошибку
    handleError(error) {
      console.log(error);
      UIkit.modal('#js-modal-error').show();
    },
  },
};
</script>
<style>
@media (max-width: 640px) {
  .cg__padding_none {
    padding: 0 0 0 0;
  }
}
</style>
