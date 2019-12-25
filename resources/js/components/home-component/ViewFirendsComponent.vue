<template>
<div class="uk-container">
    <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <h3 class="uk-card-title">{{ content.header }}</h3>
            <template v-if="friends.length !== 0">
                <ul>
                    <li v-for="(friend, index) in friends" v-bind:key="index" :class="isOnline(friend.login)">
                        {{ friend.name }} ({{ friend.login }})
                        <span><a href=""></a>Начать игру</span>
                    </li>
                </ul>
                <hr>
            </template>
            <template v-else>
                <hr>
                <p>{{ content.noFriendsText }}</p>
            </template>
            <!-- <hr> -->
            <!-- <div class="col-sm-12">
                <textarea class="form-control" rows="10" readonly="">{{messages.join('\n')}}</textarea>
                <hr>
                <input type="text" class="form-control" v-model="textMessage" @keyup.enter="sendMessage" @keydown="actionUser">
                <span v-if="isActive">{{isActive.name}} набирает сообщение...</span>
            </div>
            <div>
                <ul>
                    <li v-for="user in activeUsers">{{user}}</li>
                </ul>
            </div> -->
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
                activeUsers: []
            }
        },
        computed: {
            channel() {
                return window.Echo.join('connect');
            },
        },
        mounted() {
            console.log(this.friends);
            this.channel
                .here((users) => {
                    this.activeUsers = users;
                })
                .joining((user) => {
                    console.log(this.activeUsers);
                    this.activeUsers.push(user);
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
                })
                .listen('ConnectOnline', ({data}) => {
                    this.messages.push(data.body);
                    this.isActive = false;
                })
                .listenForWhisper('typing', (e) => {
                    this.isActive = e;
                    
                    if(this.typingTimer) clearTimeout(this.typingTimer);
                    this.typingTimer = setTimeout(() => {
                        this.isActive = false;
                    }, 2000);
                });
        },
        methods: {
            sendMessage() {

                axios.post('/messages', { body: this.textMessage, room_id: 1});
                this.messages.push(this.textMessage);
                this.textMessage = '';
            },
            actionUser() {
                this.channel
                    .whisper('typing', {
                        name: this.user.name
                    });
            },
            isOnline: function (friendLogin) {

                if (this.activeUsers.indexOf(friendLogin) !== -1)
                    return 'friends-card__item__online';
                
                return 'friends-card__item__offline';
            }
        }
    }
</script>
