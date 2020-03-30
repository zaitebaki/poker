<template>
  <div class="uk-width-auto@s uk-width-1-1">
    <div class="uk-background-muted uk-padding-small">
      <div class="uk-flex">
        <div class="uk-flex uk-flex-middle">
          <ul
            class="uk-list uk-text-light money__text_color_red uk-margin-remove"
          >
            <template v-for="(message, index) in getMessages">
              <li
                :key="index"
                :class="{money__text_color_blue: index % 2}"
              >
                {{ message.login }}: + {{ message.money }}
              </li>
            </template>
          </ul>
        </div>
        <div class="uk-flex uk-flex-middle">
          <div class="uk-padding-small">
            <svg class="money__icon">
              <use xlink:href="/assets/images/game/money-icon.svg#money" />
            </svg>
          </div>
          <div>
            <span class="uk-text money__total-text">
              {{ money }}
            </span>
          </div>
          <div>
            <span>
              <svg
                class="logo__ruble-icon_big_width"
                aria-hidden="true"
              >
                <use xlink:href="/assets/images/game/ruble-icon.svg#Capa_1" />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    money: {
      type: String,
      default: '',
    },
    bankMessages: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {};
  },
  computed: {
    getMessages() {
      const bankDataObject = {
        messages: [],
      };
      this.bankMessages.forEach(element => {
        const splits = element.split('|');
        bankDataObject.messages.push({
          login: splits[0],
          money: splits[1],
        });
      });
      return bankDataObject.messages;
    },
  },
  mounted() {},
};
</script>
