<template>
  <div
    class="uk-alert-primary"
    uk-alert
  >
    <p>{{ invitationText }}</p>
    <form
      id="startGameForm"
      :action="formRoute"
      method="POST"
    >
      <button
        class="uk-button uk-button-secondary uk-button-small"
        type="submit"
        form="startGameForm"
      >
        {{ formButtonCaption }}
      </button>
      <input
        type="hidden"
        name="_token"
        :value="csrf"
      >
      <input
        type="hidden"
        name="updateState"
        value="InitState"
      >
      <input
        type="hidden"
        name="takeInvitationRequest"
        value="true"
      >
      <input
        type="hidden"
        name="opponentId"
        :value="opponentId"
      >
    </form>
  </div>
</template>

<script>
export default {
  props: {
    invitationText: {
      type: String,
      default: '',
    },
    formRoute: {
      type: String,
      default: '',
    },
    formButtonCaption: {
      type: String,
      default: '',
    },
    opponentId: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content'),
    };
  },
};
</script>
