<template>
  <div class="uk-container">
    <!-- <div uk-grid> -->
    <div
      class="uk-card uk-card-default uk-card-body uk-margin-small-bottom uk-width-1-1"
    >
      <div>
        <div class="uk-flex uk-margin-small-bottom">
          <div class="uk-flex uk-flex-middle">
            <p class="header__user-name header__status-text uk-margin-remove">
              {{ userParameters.name }}
            </p>
          </div>
          <div class="uk-flex uk-flex-middle">
            <span
              class="uk-margin-medium-left uk-margin-medium-right"
              uk-icon="chevron-right"
            />
          </div>
        </div>

        <!-- баланс -->
        <div class="uk-flex uk-margin-small-bottom">
          <div class="uk-flex uk-flex-middle">
            <div class="user-bar__money-div">
              <p
                class="uk-text user-bar__balance-text header__status-text uk-margin-remove"
              >
                Баланс:
                {{ userParameters.balance }}
              </p>
            </div>
            <div>
              <svg
                class="logo__ruble-icon_home_size"
                aria-hidden="true"
              >
                <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1" />
              </svg>
            </div>
          </div>
        </div>

        <!-- в, п -->
        <div class="uk-flex">
          <div class="uk-flex uk-flex-middle user-bar__victory-div">
            <p class="header__victory-text header__status-text uk-margin-remove">
              /в:
              <span>
                {{ userParameters.victory }}
              </span>
            </p>
          </div>
          <div class="uk-flex uk-flex-middle">
            <p
              class="user-bar__gameover-text header__status-text uk-margin-remove"
            >
              п:
              <span> {{ userParameters.gameover }}/ </span>
            </p>
          </div>
        </div>
      </div>
      <!-- <div class="uk-flex">
        <div class="uk-flex uk-flex-middle">
          <p class="header__user-name header__status-text uk-margin-remove">
            {{ userParameters.name }}
          </p>
        </div>
        <div class="uk-flex uk-flex-middle">
          <span
            class="uk-margin-medium-left uk-margin-medium-right"
            uk-icon="chevron-right"
          />
        </div>
        <div class="uk-flex uk-flex-middle">
          <div class="user-bar__money-div">
            <p
              class="uk-text user-bar__balance-text header__status-text uk-margin-remove"
            >
              Баланс:
              {{ userParameters.balance }}
            </p>
          </div>
          <div>
            <svg
              class="logo__ruble-icon_home_size"
              aria-hidden="true"
            >
              <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1" />
            </svg>
          </div>
        </div>
        <div class="uk-flex uk-flex-middle">
          <span
            class="uk-margin-medium-left uk-margin-medium-right"
            uk-icon="chevron-right"
          />
        </div>
        <div class="uk-flex uk-flex-middle user-bar__victory-div">
          <p class="header__victory-text header__status-text uk-margin-remove">
            /в:
            <span>
              {{ userParameters.victory }}
            </span>
          </p>
        </div>
        <div class="uk-flex uk-flex-middle">
          <p
            class="user-bar__gameover-text header__status-text uk-margin-remove"
          >
            п:
            <span> {{ userParameters.gameover }}/ </span>
          </p>
        </div>
      </div>
    </div> -->
    <!-- </div> -->
    </div>
  </div>
</template>

<script>
export default {
  props: {
    userParameters: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {};
  },

  mounted() {},

  methods: {},
};
</script>
