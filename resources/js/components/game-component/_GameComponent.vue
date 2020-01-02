<template>
<div class="uk-container">
    <game-bank-component></game-bank-component>

    <!-- <game-status-bar-component></game-status-bar-component> -->

    <game-status-text-component
        :status-message="gameStatusMessage">
    </game-status-text-component>

    <game-button-panel-component
        :buttons-captions="content.buttonsCaptions"
        :start-game-button-ready="startGameButtonReady">
    </game-button-panel-component>
    <!-- <game-user-cards-component></game-user-cards-component>  -->
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
        statusMessage: String
    },
    data() {
        return {
            startGameButtonReady: false,
            gameStatusMessage: this.statusMessage
        }
    },
    computed: {
        // gameChannel() {
        //     return window.Echo.join('room.1');
        // },
        gameActionChannel() {
            return window.Echo.private('room-action.1');
        }
    },
    mounted() {
        this.gameActionChannel
            .listen('SendReadyStatus', ({data}) => {
                   
                axios.post('/game/room/1', { updateState: 'ReadyState', srcUserLogin: this.user.login}).then( (response) => {
                    this.gameStatusMessage = response.data;
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
                this.startGameButtonReady = true;
            })

        //     .here((users) => {
        //         this.activeUsers = users;
        //     })
        //     .joining((user) => {
        //         console.log(this.activeUsers);
        //         this.activeUsers.push(user);
        //     })
        //     .leaving((user) => {
        //         this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
        //     })
            // .listen('PrivateChat', ({data}) => {
            //     console.log(data.body);
            //     this.messages.push(data.body);
            //     this.isActive = false;
            // })
            // .listenForWhisper('typing', (e) => {
            //     this.isActive = e;

            //     if(this.typingTimer) clearTimeout(this.typingTimer);
            //     this.typingTimer = setTimeout(() => {
            //         this.isActive = false;
            //     }, 2000);
            // });
    },
    // startGame: function(friendLogin) {
    //     axios.post('/startGame', { srcUserId: this.user.id, dstUserLogin: friendLogin}).then(function (response) {


            // if (response.redirect) {
            //     console.log('redirect'); 
            // }
            // if(response.data === 'STATUS_OK') {
            //     console.log('Приглашение успешно отправлено!');
            // }
            // window.location.href = response.data;
            // console.log(response);
        // }).catch(function (error) {
        //     console.log(error);
        //     alert('Не удалось отправить запрос. Повторите попытку позже.');
        // })
    // },

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
