<template>
<div class="uk-container">

    <invitation-alert-card-component
        v-if="this.isSendInvitation"
        :invitation-text="invitationText"
        :form-route="formStartGameRoute"
        :form-button-caption="invitationCardContent.formButtonCaption">
    </invitation-alert-card-component>
    
    <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title">{{ content.header }}</h3>
        <template v-if="friends.length !== 0">
            <ul>
                <li v-for="(friend, index) in friends" v-bind:key="index" :class="isOnline(friend.login)">
                    {{ friend.name }} ({{ friend.login }})|
                    <span><a v-on:click.prevent="sendInvitation(friend.login)"> {{ content.startGameText }}</a></span>
                </li>
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
        formStartGameRoute: String
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
            invitationText: ''
        }
    },
    computed: {
        connectChannel() {
            return window.Echo.join('connect');
        },
        invitationChannel() {
            console.log(this.user.id);
            return window.Echo.private('invitation.' + this.user.id);
        }
    },
    mounted() {
        console.log(this.invitationCardContent);
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
            .listen('SendInvitation', ({srcUserLogin}) => {

                this.invitationText = this.invitationCardContent.text;

                this.invitationText = this.invitationText.replace(/:name/i, srcUserLogin);

                this.isSendInvitation = true;
            })
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
        sendInvitation: function(friendLogin) {
            axios.post('/invitation', { srcUserId: this.user.id, dstUserLogin: friendLogin}).then(function (response) {
                if(response.data === 'STATUS_OK') {
                    console.log('Приглашение успешно отправлено!');
                }
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            })
        }
    },

    components: {
        'invitation-alert-card-component': InvitationAlertCardComponent
    }
}
</script>
