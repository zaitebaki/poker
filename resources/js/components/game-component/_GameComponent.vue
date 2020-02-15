<template>
<div class="uk-container">

    <game-status-bar-component
        :content="content.header"
        :user="userParameters"
        :room-id="gameParameters.roomId"
        :room-name="roomName">
    </game-status-bar-component>

    <div class="uk-flex">
        <game-bank-component
            :money=vueGameParameters.money
            :bank-messages=vueGameParameters.bankMessages>
        </game-bank-component>

        <div class="uk-width-expand uk-flex uk-flex-middle"
            v-bind:class="getBackgroundColorClass">
            <game-status-text-component
                :status-message="vueGameParameters.statusMessage"
                :indicator-status="vueGameParameters.indicator"
                :is-victory="getVictoryStatus">
            </game-status-text-component>
        </div>
    </div>

    <game-opponent-cards-component
        v-if="vueGameParameters.opponentUserCards"
        :cards="vueGameParameters.opponentUserCards"
        :combination="vueGameParameters.opponentCombination"
        :points="vueGameParameters.opponentPoints">
    </game-opponent-cards-component>

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
        @update:parameters="updateParameters($event)">
    </game-button-panel-component>

    <game-user-cards-component
        v-if="vueGameParameters.userCards"
        :cards="vueGameParameters.userCards"
        :combination="vueGameParameters.userCombination"
        :points="vueGameParameters.userPoints"
        :is-already-changed-cards="vueGameParameters.isAlreadyChangedCards"
        @change:active:cards:storage="changeActiveCardsStorage($event)">
    </game-user-cards-component>

    <!-- модальное окно о завершении сеанса игры -->
    <div id="js-modal-dialog" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title"></h2>
            <p class="uk-text-center uk-text-danger"> {{ getFinishGameMessage }}</p>
            <button class="uk-modal-close-outside" type="button" uk-close></button>
            <p class="uk-text-center">
                <button 
                    class="uk-button uk-button-danger uk-modal-close" type="button"
                    v-on:click="finishGameButtonClick()">
                    ok
                </button>
            </p>
        </div>
    </div>
</div>
</template>
<script>
import GameStatusBarComponent from './GameStatusBarComponent'
import GameBankComponent from './GameBankComponent'
import GameButtonPanelComponent from './GameButtonPanelComponent'
import GameStatusTextComponent from './GameStatusTextComponent'
import GameUserCardsComponent from './GameUserCardsComponent'
import GameOpponentCardsComponent from './GameOpponentCardsComponent'
import GameIndicatorComponent from './GameIndicatorComponent'

export default {
     props: {
        content: Object,
        user: Object,
        gameParameters: Object
    },
    data() {
        return {
            vueGameParameters: this.gameParameters,
            userParameters: this.user,
            activeCardsStorage: [true, true, true, true, true],
            combinationTable: {
                'POKER': 'Покер',
                'STREETFLASH': 'Стрит флеш',
                'KARE': 'Каре',
                'FULLHOUSE': 'Фулхауз',
                'FLASH': 'Флеш',
                'STREET': 'Стрит',
                'TROIKA': 'Тройка',
                'TWO_PAIRS': 'Две пары',
                'DVOIKA': 'Двойка',
                'WASTE': 'Хлам'
            },
            roomUrl: '/game/room/' + this.gameParameters.roomId,
            roomName: 'room_' + this.gameParameters.roomId,
            isFinishGameComponentVisible: false,
            opponentUserName: ''
        }
    },
    computed: {
        gameActionChannel() {
            return window.Echo.private('room-action.' + this.vueGameParameters.roomId);
        },
        getVictoryStatus() {
            if('isVictory' in this.vueGameParameters) {
                return this.vueGameParameters.isVictory;
            }
            return -2;
        },
        getBackgroundColorClass() {
            if(this.vueGameParameters.isVictory === -1) {
                return 'status-text__background_color_red';
            }
            return (this.vueGameParameters.indicator === 'wait') ? 'status-text__background_color_orange' : 
            'status-text__background_color_green';
        },
        getFinishGameMessage() {
            const re = /:user/gi;
            return this.content.alertMessages.finishGameSessionMessage.replace(re, this.opponentUserName);
        },
    },
    mounted() {
        this.$root.$on('changed:cards:false', () => {
            this.activeCardsStorage = [true, true, true, true, true];
        });
        this.gameActionChannel
            .listen('SendReadyStatus', ({data}) => {
                axios.post(this.roomUrl, { updateState: 'ReadyState', roomName: this.roomName, sendPost: 'true'}).then( (response) => {
                    console.log(this.roomName);
                    console.log("Пришел ответ после отправки SendReadyStatus");
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
                this.startGameButtonReady = true;
            })
            .listen('SendStartChangeCardsStatus', ({data}) => {
                axios.post(this.roomUrl, { updateState: 'StartedGameState', roomName: this.roomName}).then( (response) => {
                    console.log('SendStartChangeCardsStatus');
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
                this.startGameButtonReady = true;
            })
            .listen('SendStartedGameStatus', ({data}) => {
                console.log("Hello from SendStartedGameStatus!!!");
                axios.post(this.roomUrl, { initAction: 'startGame', roomName: this.roomName}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                    this.$root.$emit('changed:cards:false');
                    // if (this.startGameSessionFlag) {
                        // this.startChangeCardsEvent();
                        // this.startGameSessionFlag = false;
                    // }
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendBettingStatus', ({data}) => {
                console.log("Hello from SendBettingStatus!!!");
                axios.post(this.roomUrl, { updateState: 'StartedGameState', roomName: this.roomName}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendFinishChangeStatus', ({data}) => {
                console.log("Hello from SendFinishChangeStatus!!!");
                axios.post(this.roomUrl, {
                    updateState: 'BettingState',
                    roomName: this.roomName,
                    correctionStatusMessage: 'changeFinished'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendFinishBettingStatus', ({money, moneyIncrease}) => {
                console.log("Hello from SendFinishBettingStatus!!!");

                if(moneyIncrease === 'drop' || moneyIncrease === 'opponentCheck') {
                    this.sendFinishGameRequest();
                }
                else {
                    let correctionMessage;
                    if (moneyIncrease !== '0' && moneyIncrease !== 'equal') {
                        correctionMessage = "equalAndAdd"
                    }
                    else if (money === '0' && moneyIncrease === '0') {
                        correctionMessage = 'check';
                    }
                    else if (money !== '0' && moneyIncrease === 'equal') {
                        moneyIncrease = '';
                        correctionMessage = 'equal';
                    }
                    else if (money !== '0' && moneyIncrease === 'drop') {
                        moneyIncrease = '';
                        correctionMessage = 'drop';
                    }
                    else {
                        correctionMessage = 'betFinished';
                    }
                    axios.post(this.roomUrl, {
                        updateState: 'BettingState',
                        roomName: this.roomName,
                        correctionStatusMessage: correctionMessage,
                        money: money,
                        moneyIncrease: moneyIncrease}).then( (response) => {
                            this.vueGameParameters = response.data.gameParameters;
                            if (correctionMessage === "equal" || correctionMessage === "drop") {
                                this.sendFinishGameRequest();
                            }
                    }).catch(function (error) {
                        console.log(error);
                        alert('Не удалось отправить запрос. Повторите попытку позже.');
                    });
                }
            })
            .listen('SendUpdateIndicatorButtonStatus', ({data}) => {
                console.log('SendUpdateIndicatorButtonStatus');
                this.vueGameParameters.newGameButtonIndicator = false;
            })
            .listen('SendUpdateIndicatorStartButtonStatus', ({data}) => {
                console.log('SendUpdateIndicatorStartButtonStatus');
                this.vueGameParameters.startButtonIndicator = false;
            }).
            listen('SendFinishGameSessionStatus', ({opponentUserName}) => {
                console.log("Hello from SendFinishGameSessionStatus!!!");
                this.opponentUserName = opponentUserName;
                UIkit.modal('#js-modal-dialog').show();
            })
        UIkit.util.on('#js-modal-dialog', 'click', function (e) {
            e.preventDefault();
            e.target.blur();
             window.location.replace('/home');
       });
    },
    methods: {
        updateParameters($event) {
            if("user" in $event ) {
                this.userParameters = $event.user;
            }
            if("gameParameters" in $event ) {
                this.vueGameParameters = $event.gameParameters;
            }
            console.log(this.vueGameParameters.userCards);
        },
        changeActiveCardsStorage($event) {
            const index = $event;
            this.activeCardsStorage[index] = !this.activeCardsStorage[index];

            console.log(this.activeCardsStorage);
        },
        sendFinishGameRequest() {
            axios.post(this.roomUrl, {
                updateState: 'FinishState',
                roomName: this.roomName
                }).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                    this.userParameters = response.data.user;
                    // сделать кнопку "продолжить" доступной у оппонента
                    this.sendResponseAfterFinishEvent();
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },
        getCardsArr() {
            let array = [];
            console.log(this.gameParameters.userCards);
            if("userCards" in this.vueGameParameters && this.vueGameParameters.userCards) {
                this.vueGameParameters.userCards.forEach(function(item, i, arr) {
                    let obj = {
                        index: i,
                        value: item
                    }
                   array.push(obj);
                });
            }
            return array;
        },
        sendResponseAfterFinishEvent() {
            console.log('DELETE');
            axios.post(this.roomUrl, { initAction: 'delNewGameButtonIndicator', roomName: this.roomName}).then( (response) => {
                console.log('delNewGameButtonIndicator');
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },
        finishGameButtonClick() {
            window.location.replace('/home');
        }
    },
    components: {
        'game-status-bar-component': GameStatusBarComponent,
        'game-bank-component': GameBankComponent,
        'game-button-panel-component': GameButtonPanelComponent,
        'game-status-text-component': GameStatusTextComponent,
        'game-user-cards-component': GameUserCardsComponent,
        'game-opponent-cards-component': GameOpponentCardsComponent,
        'game-indicator-component': GameIndicatorComponent
    }
}

</script>
<style>

</style>
