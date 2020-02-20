<template>
  <form
    id="loginForm"
    class="uk-align-center"
    :action="formRoute"
    method="POST"
  >
    <input
      type="hidden"
      name="_token"
      :value="csrf"
    >
    <input
      type="hidden"
      name="typeForm"
      value="login"
    >
    <h3 class="uk-card-title">
      {{ propsArray.formCaption }}
    </h3>
    <fieldset class="uk-fieldset">
      <div class="uk-inline uk-width-1-1 uk-margin-bottom">
        <span
          class=" uk-form-icon"
          uk-icon="icon: user"
        />
        <input
          id="entry-login"
          class="uk-input"
          type="text"
          name="login"
          :value="oldLogin"
          required
          autofocus
          :placeholder="propsArray.inputLoginCaption"
        >
      </div>
      <div class="uk-inline uk-width-1-1 uk-margin-bottom">
        <span
          class=" uk-form-icon"
          uk-icon="icon: lock"
        />
        <input
          id="password"
          class="uk-input uk-width-1-1"
          type="password"
          name="password"
          :placeholder="propsArray.inputPasswordCaption"
          required
        >
      </div>

      <button
        class="uk-button uk-width-1-1"
        type="submit"
        form="loginForm"
      >
        {{ propsArray.formButtonCaption }}
      </button>

      <ul>
        <li
          v-for="(error, index) in oldErrors"
          :key="index"
          class="uk-text-danger"
        >
          {{ error }}
        </li>
      </ul>

      <label>
        <p class="uk-text-center uk-margin-medium-top">
          {{ propsArray.formDescription }}
          <a @click="$emit('enterRegistration')">{{
            propsArray.formOtherAction
          }}</a>
        </p>
      </label>
    </fieldset>
  </form>
</template>

<script>
export default {
  props: {
    propsArray: {
      type: Object,
      required: true,
    },
    formRoute: {
      type: String,
      default: '',
    },
    oldLogin: {
      type: String,
      default: '',
    },
    oldErrors: {
      type: Array,
      default: () => [],
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
};
</script>

<style scoped></style>
