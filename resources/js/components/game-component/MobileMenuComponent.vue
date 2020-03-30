<template>
  <div class="mmc__main-div">
    <div class="mmc__text-div">
      <a
        class="uk-button uk-button-default"
        href="#modal-sections"
        uk-toggle
      >Закончить игру</a>
    </div>
    <div
      id="modal-sections"
      uk-modal
    >
      <div class="uk-modal-dialog">
        <button
          class="uk-modal-close-default"
          type="button"
          uk-close
        />
        <div class="uk-modal-header">
          <h2 class="uk-modal-title">
            Закончить игру
          </h2>
        </div>
        <div class="uk-modal-body">
          <p>Вы действительно хотите закончить игру?</p>
        </div>
        <div class="uk-modal-footer uk-text-right">
          <button
            class="uk-button uk-button-default uk-modal-close"
            type="button"
          >
            Нет
          </button>
          <button
            class="uk-button uk-button-primary"
            type="button"
            @click="finishGameSession"
          >
            Да
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    roomId: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      roomUrl: `/game/room/${this.roomId}/finish_game_session`,
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content'),
    }
  },
  methods: {
    // закончить сеанс игры
    finishGameSession() {
      axios
        .post(this.roomUrl, {})
        .then(() => {
          window.location.replace('/home');
        })
        .catch(error => {
          this.$emit('catch:error', error);
        });
    },
  },
}
</script>
<style scoped>
@media (max-width: 640px) {
  .mmc__main-div {
    width: 100%;
    height: 220px;
    background-color: white;
    position: relative;
  }
  .mmc__text-div {
      position: absolute;
      width: 100%;
      height: auto;
      left: 0px;
      bottom: 15px;
      color: white;
      text-align: center;
  }
}
</style>