<template>
<div class="uk-container">
    <div>
        <h4>{{ content.header }}</h4>
        <div
            v-if="status === 'success'"
            class="status-text__background_color_green">
            <p>{{ successMessage }}</p>
        </div>

        <table class="uk-table uk-table-divider">
            <tbody>
                <tr v-for="payment in payments" v-bind:key=payment.login>
                    <td
                        class="uk-table-shrink"
                        :class="paymentMessageClass(payment.moneyValue)">
                        {{ getCreditMessage(payment) }}
                    </td>
                    <td
                        v-if="payment.moneyValue > 0"
                        class="uk-table-shrink">
                        <form
                            :id="getIdForm(payment)"
                            :action="cancelPaymentRoute"
                            method="POST">
                            <button
                                class="uk-button uk-button-secondary uk-button-small"
                                type="submit"
                                :form="getIdForm(payment)">
                                {{ content.incomeButtonCaption }}
                            </button>
                            <input type="hidden" name="_token" :value="csrf">
                            <input type="hidden" name="data" :value="getJsonData(payment)">
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</template>

<script>
export default {
    props: {
        content: Object,
        payments: Array,
        cancelPaymentRoute: String,
        status: String,
        sessionStatusUserLogin: String
    },
    data() {
        return {
            urlPost: '/game/cancel_payment',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    computed: {
        successMessage: function() {
            return this.content.successMessage.replace(/:user/gi, this.sessionStatusUserLogin);
        }
    },
    methods: {
        // получить "финансовое сообщение"
        getCreditMessage(payment) {
            const userFullName = `${payment.nameOpponent} (${payment.loginOpponent})`;
            let message = '';
            let money = 0;
            if(payment.moneyValue > 0) {
                message = this.content.income;
                money = payment.moneyValue;
            }
            else {
                message = this.content.credit;
                money = -payment.moneyValue;
            }

            message = message.replace(/:user/i, userFullName);
            message = message.replace(/:money/i, money);
            return message;
        },

        getJsonData(payment) {
            return JSON.stringify(payment);
        },

        getIdForm(payment) {
            return 'delPaymentForm_' + payment.loginOpponent;
        },

        paymentMessageClass(value) {
            if(value > 0) {
                return 'friends-card__item__online';
            }
            return 'header__gameover-text';
        }
    }
}

</script>
<style scoped>
</style>
