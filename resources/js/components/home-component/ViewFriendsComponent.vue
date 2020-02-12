<template>
<div class="uk-container">

    <invitation-alert-card-component
        v-if="this.isSendInvitation"
        :invitation-text="invitationText"
        :form-route="formJoinGameRoute"
        :form-button-caption="invitationCardContent.formButtonCaption"
        :opponent-id="curSrcUserId">
    </invitation-alert-card-component>
    
    <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title">{{ content.header }}</h3>
        <template v-if="friends.length !== 0">
            <ul class="uk-list">               
                <template v-for="(friend, index) in friends">
                    <li v-if="isOnline(friend.login)" v-bind:key="friend.login" class="friends-card__item__online">
                        {{ friend.name }}-{{ friend.login }} |
                        <span>
                            <form :id="'sendInvitationForm' + index" :action="formJoinGameRoute" method="POST">
                                <button
                                    class="uk-button uk-button-secondary uk-button-small"
                                    type="submit"
                                    :form="'sendInvitationForm' + index">
                                    {{ content.startGameText }}
                                </button>
                                <input type="hidden" name="_token" :value="csrf">
                                <input type="hidden" name="sendInvitationRequest" value="true">
                                <input type="hidden" name="updateState" value="InitState">
                                <input type="hidden" name="opponentId" :value="friend.id">
                            </form>
                        </span>
                    </li>
                    <li v-else v-bind:key="index" class="friends-card__item__offline">
                        {{ friend.name }}-{{ friend.login }}
                    </li>
                </template>
            </ul>
            <hr>
        </template>
        <template v-else>
            <hr>
            <p>{{ content.noFriendsText }}</p>
        </template>
    </div>
</div>
</template>

<script>

import InvitationAlertCardComponent from './InvitationAlertCardComponent'

export default {
    props: {
        content: Object,
        invitationCardContent: Object,
        friends: Array,
        user: Object,
        formJoinGameRoute: String
    },
    data() {
        return {
            messages: [],
            textMessage: '',
            isActive: false,
            typingTimer: false,
            activeUsers: [],
            currenUserIndex: undefined,
            isSendInvitation: false,
            curSrcUserId: '',
            invitationText: '',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    computed: {
        connectChannel() {
            return window.Echo.join('connect');
        },
        invitationChannel() {
            return window.Echo.private('invitation.' + this.user.id);
        }
    },
    mounted() {
        console.log(this.friends);
        this.connectChannel
            .here((users) => {
                this.activeUsers = users;
            })
            .joining((user) => {
                console.log(this.activeUsers);
                this.activeUsers.push(user);
            })
            .leaving((user) => {
                this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
            }),
        this.invitationChannel
            .listen('SendInvitation', ({srcUserId, srcUserLogin}) => {

                this.invitationText = this.invitationCardContent.text;
                this.invitationText = this.invitationText.replace(/:name/i, srcUserLogin);
                this.curSrcUserId = srcUserId;
                this.curSrcUserLogin = srcUserLogin;
                this.isSendInvitation = true;
            })
    },
    methods: {
        // sendMessage() {
        //     axios.post('/messages', { body: this.textMessage, room_id: 1});
        //     this.messages.push(this.textMessage);
        //     this.textMessage = '';
        // },
        // actionUser() {
        //     this.channel
        //         .whisper('typing', {
        //             name: this.user.name
        //         });
        // },
        isOnline: function (friendLogin) {
            if (this.activeUsers.indexOf(friendLogin) !== -1)
                return 'friends-card__item__online';
            
            return 'friends-card__item__offline';
        },
        isOnline: function (friendLogin) {
            if (this.activeUsers.indexOf(friendLogin) !== -1)
                return true;
            
            return false;
        },
        // sendInvitation: function(friendLogin) {
        //     axios.post('/invitation', { srcUserId: this.user.id, dstUserLogin: friendLogin}).then(function (response) {


                // if (response.redirect) {
                //     console.log('redirect'); 
                // }
                // if(response.data === 'STATUS_OK') {
                //     console.log('Приглашение успешно отправлено!');
                // }
                // window.location.href = response.data;
                // console.log(response);
        //     }).catch(function (error) {
        //         console.log(error);
        //         alert('Не удалось отправить запрос. Повторите попытку позже.');
        //     })
        // }
    },

    components: {
        'invitation-alert-card-component': InvitationAlertCardComponent
    }
}
</script>
