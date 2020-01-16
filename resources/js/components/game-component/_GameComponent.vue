<template>
<div class="uk-container">
    <div class="uk-flex">
        <game-bank-component
            :money=vueGameParameters.money
            :bank-messages=vueGameParameters.bankMessages>
        </game-bank-component>

        <game-opponent-cards-component
            v-if="vueGameParameters.opponentUserCards"
            :cards="vueGameParameters.opponentUserCards"
            :combination="vueGameParameters.opponentCombination"
            :points="vueGameParameters.opponentPoints">
        </game-opponent-cards-component>
    </div>

    <!-- <game-status-bar-component></game-status-bar-component> -->
    <game-indicator-component
        :indicator-status="vueGameParameters.indicator">
    </game-indicator-component>

    <game-status-text-component
        :status-message="vueGameParameters.statusMessage">
    </game-status-text-component>

    <game-button-panel-component
        :buttons-captions="content.buttonsCaptions"
        :buttons="vueGameParameters.buttons"
        :user="user"
        :active-cards-storage="activeCardsStorage"
        :indicator-status="vueGameParameters.indicator"
        :money="vueGameParameters.money"
        :add-opponent-money="vueGameParameters.addOpponentMoney"
        :increase-after-equal-money="vueGameParameters.increaseAfterEqualMoney"
        @update:parameters="updateParameters($event)">
    </game-button-panel-component>

    <game-user-cards-component
        v-if="vueGameParameters.userCards"
        :cards="vueGameParameters.userCards"
        :combination="vueGameParameters.userCombination"
        :points="vueGameParameters.userPoints"
        @change:active:cards:storage="changeActiveCardsStorage($event)">
    </game-user-cards-component>


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
            cards: null,
            vueGameParameters: this.gameParameters,
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
        }
    },
    computed: {
        gameActionChannel() {
            return window.Echo.private('room-action.1');
        },
    },
    mounted() {
        console.log(this.gameParameters);
        this.gameActionChannel
            .listen('SendReadyStatus', ({data}) => {
                axios.post('/game/room/1', { updateState: 'ReadyState', roomName: 'room_1', sendPost: 'true'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
                this.startGameButtonReady = true;
            })
            .listen('SendStartedGameStatus', ({data}) => {
                console.log("Hello from SendStartedGameStatus!!!");
                axios.post('/game/room/1', { initAction: 'startGame', roomName: 'room_1'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendBettingStatus', ({data}) => {
                console.log("Hello from SendBettingStatus!!!");
                axios.post('/game/room/1', { updateState: 'StartedGameState', roomName: 'room_1'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendFinishChangeStatus', ({data}) => {
                console.log("Hello from SendFinishChangeStatus!!!");
                axios.post('/game/room/1', {
                    updateState: 'BettingState',
                    roomName: 'room_1',
                    correctionStatusMessage: 'changeFinished'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
            .listen('SendFinishBettingStatus', ({money, moneyIncrease}) => {
                console.log("Hello from SendFinishBettingStatus!!!");
                let correctionMessage;

                if (moneyIncrease !== '0' && moneyIncrease !== 'equal') {
                    correctionMessage = "equalAndAdd"
                }
                else if (money === '0' && moneyIncrease === '0') {
                    correctionMessage = 'check';
                }
                else if (money !== '0' && moneyIncrease === 'equal') {
                    correctionMessage = 'equal';
                }
                else {
                    correctionMessage = 'betFinished';
                }
                axios.post('/game/room/1', {
                    updateState: 'BettingState',
                    roomName: 'room_1',
                    correctionStatusMessage: correctionMessage,
                    money: money,
                    moneyIncrease: moneyIncrease}).then( (response) => {
                        this.vueGameParameters = response.data.gameParameters;
                        if (correctionMessage === "equal") {
                            this.senFinishGameRequest();
                        }
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            })
    },
    methods: {
        updateParameters($event) {
            this.vueGameParameters = $event;
        },
        changeActiveCardsStorage($event) {
            const index = $event;
            this.activeCardsStorage[index] = !this.activeCardsStorage[index];
        },
        senFinishGameRequest() {
            axios.post('/game/room/1', {
                updateState: 'FinishState',
                roomName: 'room_1'
                }).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        }
    },
    components: {
        'game-status-bar-component': GameStatusBarComponent,
        'game-bank-component': GameBankComponent,
        'game-button-panel-component': GameButtonPanelComponent,
        'game-status-text-component': GameStatusTextComponent,
        'game-user-cards-component': GameUserCardsComponent,
        'game-opponent-cards-component': GameOpponentCardsComponent,
        'game-indicator-component': GameIndicatorComponent,
    }
}

</script>
<style>

</style>
