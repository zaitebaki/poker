<template>
  <header>
    <header-component :name-project="nameProject" />
    <user-status-component :user-parameters="user" />

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
      :session-status-user-login="sessionStatusUserLogin"
    />
  </header>
</template>

<script>
import HeaderComponent from './HeaderComponent.vue';
import UserStatusComponent from './UserStatusComponent.vue';
import FriendsAndPaymentsComponent from './FriendsAndPaymentsComponent.vue';

export default {
  components: {
    'header-component': HeaderComponent,
    'user-status-component': UserStatusComponent,
    'friends-and-payments-component': FriendsAndPaymentsComponent,
  },
  props: {
    nameProject: {
      type: String,
      default: '',
    },
    content: {
      type: Object,
      required: true,
    },
    contentPayments: {
      type: Object,
      required: true,
    },
    invitationCardContent: {
      type: Object,
      required: true,
    },
    friends: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      required: true,
    },
    payments: {
      type: Array,
      default: () => [],
    },
    formJoinGameRoute: {
      type: String,
      default: '',
    },
    cancelPaymentRoute: {
      type: String,
      default: '',
    },
    status: {
      type: String,
      default: '',
    },
    sessionStatusUserLogin: {
      type: String,
      default: '',
    },
  },

  mounted() {},
};
</script>
