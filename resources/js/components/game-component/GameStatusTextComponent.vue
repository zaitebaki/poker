<template>
  <div class="uk-flex uk-flex-middle uk-padding">
    <div>
      <p class="uk-margin-remove">
        {{ statusMessage }}
      </p>
    </div>
    <div
      v-if="indicatorStatus === 'wait'"
      class="uk-padding-small"
    >
      <img
        src="/assets/images/game/waiting.gif"
        alt
      >
    </div>
    <div
      v-if="isVictory !== -2"
      class="message-block__smile"
    >
      <img
        class="message-block__smile-img"
        :src="getPathToSmile()"
        alt
      >
    </div>
  </div>
</template>

<script>
export default {
  props: {
    statusMessage: {
      type: String,
      default: '',
    },
    indicatorStatus: {
      type: String,
      default: '',
    },
    isVictory: {
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
  mounted() {},
  methods: {
    getPathToSmile() {
      let randSmileNumber;
      let correctPath;
      if (this.isVictory === -1) {
        randSmileNumber = this.getRandomIntInclusive(1, 9);
        correctPath = 'gameover';
      }
      if (this.isVictory === 1) {
        randSmileNumber = this.getRandomIntInclusive(1, 7);
        correctPath = 'win';
      }
      return `/assets/images/game/smiles/${correctPath}/${randSmileNumber}.png`;
    },

    // получить случайное целое число
    // максимум и минимум включаются
    getRandomIntInclusive(min, max) {
      const minValue = Math.ceil(min);
      const maxValue = Math.floor(max);
      return Math.floor(Math.random() * (maxValue - minValue + 1)) + minValue;
    },
  },
};
</script>
