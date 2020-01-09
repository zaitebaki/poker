<template>
<div class="uk-container">
    <game-bank-component
        :money=vueGameParameters.money>

    </game-bank-component>

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
        @update:parameters="updateParameters($event)">
    </game-button-panel-component>

    <game-user-cards-component
        v-if="vueGameParameters.userCards"
        :cards="vueGameParameters.userCards"
        @change:active:cards:storage="changActiveCardsStorage($event)">
    </game-user-cards-component>
    <!-- <game-opponent-cards-component></game-opponent-cards-component> -->
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
            activeCardsStorage: [true, true, true, true, true]
        }
    },
    computed: {
        gameActionChannel() {
            return window.Echo.private('room-action.1');
        },
    },
    mounted() {
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
                axios.post('/game/room/1', { updateState: 'BettingState', roomName: 'room_1'}).then( (response) => {
                    this.vueGameParameters = response.data.gameParameters;
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
        changActiveCardsStorage($event) {
            const index = $event;
            this.activeCardsStorage[index] = !this.activeCardsStorage[index];

            console.log(this.activeCardsStorage);
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
