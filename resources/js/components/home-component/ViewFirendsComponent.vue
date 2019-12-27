<template>
<div class="uk-container">
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
    export default {
        props: {
            content: Object,
            friends: Array,
            user: Object
        },
        data() {
            return {
                messages: [],
                textMessage: '',
                isActive: false,
                typingTimer: false,
                activeUsers: [],
                currenUserIndex: undefined
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
                .listen('SendInvitation', ({srcUserLogin}) => {

                    // console.log(`Вас пригласил в игру пользователь ${callUserLogin}!`);

                    console.log(`Вас пригласил в игру пользователь ${srcUserLogin}`);


                    // console.log(`Вас пригласил в игру пользователь ${callUserLogin}!`);

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
                console.log('hi');
                axios.post('/invitation', { srcUserId: this.user.id, dstUserLogin: friendLogin}).then(function (response) {
                    if(response.data === 'STATUS_OK') {
                        console.log('Приглашение успешно отправлено!');
                    }
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                })
            }
        }
    }
</script>
