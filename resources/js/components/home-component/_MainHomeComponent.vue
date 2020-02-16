<template>
<header>
    <header-component
        :name-project="nameProject">
    </header-component>
    <user-status-component
        :user-parameters="user">
    </user-status-component>

    <friends-and-payments-component
        :content="content"
        :invitation-card-content="invitationCardContent"
        :friends="friends"
        :user="user"
        :form-join-game-route="formJoinGameRoute"

        :content-payments="contentPayments"
        :payments="payments"
        :cancel-payment-route="cancelPaymentRoute"
        :status="status"
        :session-status-user-login="sessionStatusUserLogin">
    </friends-and-payments-component>

</header>
</template>

<script>
import HeaderComponent from './HeaderComponent'
import UserStatusComponent from './UserStatusComponent'
import FriendsAndPaymentsComponent from './FriendsAndPaymentsComponent'

export default {
    props: {
        nameProject: String,
        content: Object,
        contentPayments: Object,
        invitationCardContent: Object,
        friends: Array,
        user: Object,
        payments: Array,
        formJoinGameRoute: String,
        cancelPaymentRoute: String,
        status: String,
        sessionStatusUserLogin: String
    },

    mounted() {
        console.log('status');
        console.log(this.status);
    },

    components: {
        'header-component': HeaderComponent,
        'user-status-component': UserStatusComponent,
        'friends-and-payments-component': FriendsAndPaymentsComponent
    }
}
</script>