<template>
  <form
    id="regForm"
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
      value="registration"
    >
    <h3 class="uk-card-title">
      {{ propsArray.formCaption }}
    </h3>
    <fieldset class="uk-fieldset">
      <div class="uk-inline uk-width-1-1 uk-margin-bottom">
        <span
          class=" uk-form-icon"
          uk-icon="icon: happy"
        />
        <input
          id="name"
          class="uk-input"
          type="text"
          name="name"
          :value="oldName"
          required
          autofocus
          :placeholder="propsArray.inputNameCaption"
        >
      </div>
      <div class="uk-inline uk-width-1-1 uk-margin-bottom">
        <span
          class=" uk-form-icon"
          uk-icon="icon: user"
        />
        <input
          id="reg-login"
          class="uk-input"
          type="text"
          name="reg-login"
          :value="oldRegLogin"
          required
          :placeholder="propsArray.inputLoginCaption"
        >
      </div>
      <div class="uk-inline uk-width-1-1 uk-margin-bottom">
        <span
          class=" uk-form-icon"
          uk-icon="icon: lock"
        />
        <input
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
        form="regForm"
      >
        {{ propsArray.formButtonCaption }}
      </button>

      <ul>
        <li
          v-for="(error, index) in oldErrors"
          :key="index"
          class="uk-text-danger"
        >
          {{ error[0] }}
        </li>
      </ul>

      <label>
        <p class="uk-text-center uk-margin-medium-top">
          {{ propsArray.formDescription }}
          <a @click="$emit('enterLogin')">{{ propsArray.formOtherAction }}</a>
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
    oldName: {
      type: String,
      default: '',
    },
    oldRegLogin: {
      type: String,
      default: '',
    },
    oldErrors: {
      type: Object,
      default: () => {},
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

<style scoped></style>
