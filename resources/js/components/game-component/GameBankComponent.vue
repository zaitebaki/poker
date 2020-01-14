<template>
    <div class="uk-card uk-card-default uk-card-body uk-width-1-3@m">
        <div class="uk-flex">
            <div>
                <ul class="uk-list uk-text-small uk-text-light uk-text-success">
                    <template v-for="(message, index) in getMessages">
                        <li>{{ message.login }}: + {{ message.money }}</li>
                    </template>
                </ul>
            </div>
            <div >
                <p><img src="/assets/images/game/money-icon.svg" alt="" class="money__img">
                    <span>{{ money }} руб.</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        money: String,
        bankMessages: Array
    },
    mounted() {
        console.log(this.bankMessages);
    },
    computed: {
        getMessages() {
            let bankDataObject = {
                messages: []
            }
            this.bankMessages.forEach(element => {
                let splits = element.split('|');
                bankDataObject.messages.push({
                    login: splits[0],
                    money: splits[1],
                })
            });
            return bankDataObject.messages;
        }
    },
    data() {
        return {
            // messages: {
            //     messages: [
            //         {login: 'valera', money: 5},
            //         {login: 'victor', money: 5}
            //     ]
            // }
            // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
}
</script>