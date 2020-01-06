<template>
<div class="uk-container">
    <game-bank-component></game-bank-component>

    <!-- <game-status-bar-component></game-status-bar-component> -->

    <game-status-text-component
        :status-message="vueGameParameters.statusMessage">
    </game-status-text-component>

    <game-button-panel-component
        :buttons-captions="content.buttonsCaptions"
        :buttons="vueGameParameters.buttons"
        :user="user"
        @update:parameters="updateParameters($event)">
    </game-button-panel-component>

    <game-user-cards-component
        v-if="vueGameParameters.userCards"
        :cards="vueGameParameters.userCards">
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

export default {
     props: {
        content: Object,
        user: Object,
        gameParameters: Object
    },
    data() {
        return {
            cards: null,
            vueGameParameters: this.gameParameters
        }
    },
    computed: {
        gameActionChannel() {
            return window.Echo.private('room-action.1');
        }
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
    },
    methods: {
        updateParameters($event) {
            this.vueGameParameters = $event;
        },
    },
    components: {
        'game-status-bar-component': GameStatusBarComponent,
        'game-bank-component': GameBankComponent,
        'game-button-panel-component': GameButtonPanelComponent,
        'game-status-text-component': GameStatusTextComponent,
        'game-user-cards-component': GameUserCardsComponent,
        'game-opponent-cards-component': GameOpponentCardsComponent
    }
}

</script>
<style>

</style>
