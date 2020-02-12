<template>
<header>
    <header-component
        :name-project="nameProject">
    </header-component>
    <view-friends-component
        :content="content"
        :invitation-card-content="invitationCardContent"
        :friends="friends"
        :user="user"
        :form-join-game-route="formJoinGameRoute"
    ></view-friends-component>
    <payments-component
        :content="contentPayments"
        :payments="payments"
        :cancel-payment-route="cancelPaymentRoute"
        :status="status"
        :session-status-user-login="sessionStatusUserLogin">
    </payments-component>
</header>
</template>

<script>
import HeaderComponent from './HeaderComponent'
import ViewFriendsComponent from './ViewFriendsComponent'
import PaymentsComponent from './PaymentsComponent'

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
        'view-friends-component': ViewFriendsComponent,
        'payments-component': PaymentsComponent
    }
}
</script>