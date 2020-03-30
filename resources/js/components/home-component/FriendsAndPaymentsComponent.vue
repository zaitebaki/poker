<template>
  <div class="uk-container">
    <invitation-alert-card-component
      v-if="isSendInvitation"
      :invitation-text="invitationText"
      :form-route="formJoinGameRoute"
      :form-button-caption="invitationCardContent.formButtonCaption"
      :opponent-id="curSrcUserId"
    />

    <div uk-grid>
      <div class="uk-width-1-3@s">
        <div
          class="uk-card uk-card-default uk-card-body uk-margin-small-right@s"
        >
          <div class="uk-flex">
            <div class="uk-margin-remove">
              <h4 class="uk-margin-remove user-bar__friend-header">
                {{ content.header }}
              </h4>
            </div>
            <div class="uk-margin-remove">
              <span
                class="uk-margin-medium-left"
                uk-icon="chevron-right"
              />
            </div>
          </div>
          <template v-if="friends.length !== 0">
            <ul class="uk-list">
              <template v-for="(friend, index) in friends">
                <li
                  v-if="isOnline(friend.login)"
                  :key="friend.login"
                  class="friends-card__item__online"
                >
                  {{ friend.name }}-{{ friend.login }}
                  <span
                    uk-icon="chevron-right"
                    class="uk-margin-small-left uk-margin-small-right"
                  />
                  <form
                    :id="'sendInvitationForm' + index"
                    class="uk-inline"
                    :action="formJoinGameRoute"
                    method="POST"
                  >
                    <button
                      class="uk-button uk-button-secondary uk-button-small"
                      type="submit"
                      :form="'sendInvitationForm' + index"
                    >
                      {{ content.startGameText }}
                    </button>
                    <input
                      type="hidden"
                      name="_token"
                      :value="csrf"
                    >
                    <input
                      type="hidden"
                      name="sendInvitationRequest"
                      value="true"
                    >
                    <input
                      type="hidden"
                      name="updateState"
                      value="InitState"
                    >
                    <input
                      type="hidden"
                      name="opponentId"
                      :value="friend.id"
                    >
                  </form>
                </li>
                <li
                  v-else
                  :key="index"
                  class="friends-card__item__offline"
                >
                  {{ friend.name }}-{{ friend.login }}
                </li>
              </template>
            </ul>
          </template>
          <template v-else>
            <hr>
            <p>{{ content.noFriendsText }}</p>
          </template>
        </div>
      </div>

      <payments-component
        :content="contentPayments"
        :payments="payments"
        :cancel-payment-route="cancelPaymentRoute"
        :status="status"
        :session-status-user-login="sessionStatusUserLogin"
      /> 
    </div>
  </div>
</template>

<script>
import InvitationAlertCardComponent from './InvitationAlertCardComponent.vue';
import PaymentsComponent from './PaymentsComponent.vue';

export default {
  components: {
    'invitation-alert-card-component': InvitationAlertCardComponent,
    'payments-component': PaymentsComponent,
  },
  props: {
    content: {
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
    formJoinGameRoute: {
      type: String,
      default: '',
    },
    contentPayments: {
      type: Object,
      required: true,
    },
    payments: {
      type: Array,
      default: () => [],
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
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content'),
    };
  },
  computed: {
    connectChannel() {
      return window.Echo.join('connect');
    },
    invitationChannel() {
      return window.Echo.private(`invitation.${this.user.id}`);
    },
  },
  mounted() {
    this.connectChannel
      .here(users => {
        this.activeUsers = users;
      })
      .joining(user => {
        this.activeUsers.push(user);
      })
      .leaving(user => {
        this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
      });
    this.invitationChannel.listen(
      'SendInvitation',
      ({srcUserId, srcUserLogin}) => {
        this.invitationText = this.invitationCardContent.text;
        this.invitationText = this.invitationText.replace(
          /:name/i,
          srcUserLogin
        );
        this.curSrcUserId = srcUserId;
        this.curSrcUserLogin = srcUserLogin;
        this.isSendInvitation = true;
      }
    );
  },
  methods: {
    isOnline(friendLogin) {
      if (this.activeUsers.indexOf(friendLogin) !== -1) return true;
      return false;
    },
  },
};
</script>
