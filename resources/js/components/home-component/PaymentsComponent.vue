<template>
<div class="uk-card uk-card-default uk-card-body uk-width-expand">
    <div class="uk-flex payment-block__header">
        <div class="uk-margin-remove">
            <h4 class="uk-margin-remove user-bar__balance-text">{{ content.header }}</h4>
        </div>
        <div class="uk-margin-remove">
            <span class="uk-margin-medium-left" uk-icon="chevron-right"></span>
        </div>
    </div>
    <div
        v-if="status === 'success'"
        class="status-text__background_color_green">
        <p>{{ successMessage }}</p>
    </div>
    <table>
        <tr
            v-for="payment in payments"
            v-bind:key=payment.login>
            <td>
                <div class="uk-flex">
                    <div class="uk-margin-medium-right">
                        <p
                            :class="paymentMessageClass(payment.moneyValue)"
                            class="uk-margin-small-bottom">
                            {{ getCreditMessage(payment) }}
                        </p>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <form
                        v-if="payment.moneyValue > 0"
                        :id="getIdForm(payment)"
                        :action="cancelPaymentRoute"
                        method="POST">
                        <button
                            class="uk-button uk-button-secondary uk-button-small uk-margin-small-bottom"
                            type="submit"
                            :form="getIdForm(payment)">
                            {{ content.incomeButtonCaption }}
                        </button>
                        <input type="hidden" name="_token" :value="csrf">
                        <input type="hidden" name="data" :value="getJsonData(payment)">
                    </form>
                </div>
            </td>
        </tr>
    </table>
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
                return 'header__balance-text';
            }
            return 'user-bar__gameover-text';
        }
    }
}

</script>
<style scoped>
</style>
