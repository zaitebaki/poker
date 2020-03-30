<template>
  <div class="uk-section-secondary uk-preserve-color">
    <nav class="uk-navbar-container uk-navbar-transparent uk-light">
      <div class="uk-container uk-container-expand">
        <!-- мобильный блок для < 640px -->
        <div
          class="uk-padding-small uk-hidden@s"
        >
          <div>
            <p class="uk-text uk-margin-remove header__statistic-text">
              <a href="/">
                <img
                  src="/assets/images/logo.svg"
                  alt
                  class="logo__img"
                >
              </a>
              {{ content.balance }}:
              <span>{{ user.balance }}&nbsp;</span>
              <span>
                <svg
                  class="logo__ruble-icon"
                  aria-hidden="true"
                >
                  <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1" />
                </svg>
              </span>
              <span style="padding-left: 40px;">
                <span class="uk-text uk-text-small header__victory-text">
                  /в:
                  <span class="">{{ user.victory }}</span>
                </span>
                <span class="uk-tex uk-text-small header__gameover-text">
                  п:
                  <span>{{ user.gameover }}/</span>
                </span>
              </span>
            </p>
          </div>
        </div>

        <!-- блок для устройств > 640px -->
        <div
          uk-navbar
          class="uk-visible@s"
        >
          <div class="uk-navbar-left uk-margin-small-left">
            <a
              class="uk-navbar-item header__logo-text"
              href="/"
            >
              <img
                src="/assets/images/logo.svg"
                alt
                class="logo__img"
              >
              Зайте баки
            </a>
            <div class="uk-navbar-item">
              <span>
                <img
                  src="/assets/images/logo.svg"
                  alt
                  class="logo__img"
                >
              </span>
              <p class="uk-text uk-margin-remove header__user-name">
                {{ user.name }}
              </p>
            </div>
            <div class="uk-navbar-item">
              <span>
                <img
                  src="/assets/images/logo.svg"
                  alt
                  class="logo__img"
                >
              </span>
              <p class="uk-text uk-margin-remove header__statistic-text">
                {{ content.balance }}:
                <span>{{ user.balance }}&nbsp;</span>
              </p>
              <svg
                class="logo__ruble-icon"
                aria-hidden="true"
              >
                <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1" />
              </svg>
            </div>
          </div>
          <div class="uk-navbar-right">
            <div class="uk-navbar-item">
              <div class="uk-flex">
                <div class="uk-margin-small-right">
                  <p
                    class="uk-text uk-margin-remove uk-text-small header__victory-text"
                  >
                    /в:
                    <span>{{ user.victory }}</span>
                  </p>
                </div>
                <div>
                  <p
                    class="uk-text uk-margin-remove uk-text-small header__gameover-text"
                  >
                    п:
                    <span>{{ user.gameover }}/</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="uk-navbar-item">
              <ul class="uk-navbar-nav">
                <li>
                  <a
                    class="uk-navbar-toggle uk-text-warning"
                    href="#"
                  >
                    <span uk-navbar-toggle-icon />
                  </a>
                  <div class="uk-navbar-dropdown">
                    <ul class="uk-nav uk-navbar-dropdown-nav">
                      <li>
                        <a
                          href="#"
                          @click="finishGameSession"
                        >Закончить игру</a>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul> 
            </div>
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  props: {
    content: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    roomId: {
      type: String,
      default: '',
    },
    roomName: {
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
    };
  },

  mounted() {},

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
};
</script>
<style scoped>
  @media (max-width: 640px) {
    .header__logo-text {
      font-size: 14px;
    }
    .header__user-name {
      font-size: 14px;
    }
    .header__statistic-text {
      font-size: 13.5px;
    }
  }
</style>
