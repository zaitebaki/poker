<template>
  <div class="uk-container">
    <div
      class="uk-card uk-card-default uk-align-center uk-width-1-1
      uk-width-large@s uk-card-body uk-box-shadow-small uk-flex uk-flex-middle"
    >
      <!-- форма входа -->
      <login-form-component
        v-if="currentAction === 'login'"
        :props-array="propsArray['loginForm']"
        :form-route="formRouteLogin"
        :old-login="oldLogin"
        :old-errors="errorMessages['loginErrors']"
        @enterRegistration="enterRegistration"
      />

      <!-- форма регистрации -->
      <registration-form-component
        v-if="currentAction === 'registration'"
        :props-array="propsArray['registrationForm']"
        :form-route="formRouteRegistration"
        :old-name="oldName"
        :old-reg-login="oldRegLogin"
        :old-errors="errorRegMessages"
        @enterLogin="enterLogin"
      />
    </div>
  </div>
</template>

<script>
import LoginFormComponent from './LoginFormComponent.vue';
import RegistrationFormComponent from './RegistrationFormComponent.vue';

export default {
  components: {
    'login-form-component': LoginFormComponent,
    'registration-form-component': RegistrationFormComponent,
  },
  props: {
    typeForm: {
      type: String,
      default: '',
    },
    propsArray: {
      type: Array,
      default: () => [],
    },
    formRouteLogin: {
      type: String,
      default: '',
    },
    formRouteRegistration: {
      type: String,
      default: '',
    },
    oldLogin: {
      type: String,
      default: '',
    },
    oldRegLogin: {
      type: String,
      default: '',
    },
    oldName: {
      type: String,
      default: '',
    },
    oldErrors: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      currentAction: this.typeForm,
      message: 'Привет!',
      errorMessages: JSON.parse(this.oldErrors),
    };
  },
  computed: {
    errorRegMessages() {
      const res = _.omitBy(this.errorMessages, (value, key) =>
        key.startsWith('loginErrors')
      );
      return res;
    },
  },
  mounted() {},
  methods: {
    enterRegistration() {
      this.currentAction = 'registration';
    },

    enterLogin() {
      this.currentAction = 'login';
    },
  },
};
</script>
<style scoped></style>
