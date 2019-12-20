<template>
    <div class="uk-container">
        <div class="uk-card uk-card-default uk-align-center uk-width-1-1 uk-width-large@s uk-card-body uk-box-shadow-small uk-flex uk-flex-middle">
            
            <!-- форма входа -->
            <login-form-component :propsArray="propsArray['loginForm']"
                v-if="currentAction === 'login'"
                @enterRegistration="enterRegistration">
            </login-form-component>

            <!-- форма регистрации -->
            <registration-form-component :propsArray="propsArray['registrationForm']"
                v-if="currentAction === 'registration'"
                @enterLogin="enterLogin">
            </registration-form-component>
        </div>
    </div>
</template>

<script>
import LoginFormComponent from './LoginFormComponent'
import RegistrationFormComponent from './RegistrationFormComponent'

export default {
    props: [
        'propsArray'
    ],
    data() {
        return {
            currentAction: 'login'
        }
    },
    methods: {

        enterRegistration() {
            this.currentAction = 'registration'
        },
        
        enterLogin() {
            this.currentAction = 'login'
        }
    },
    components: {
        'login-form-component': LoginFormComponent,
        'registration-form-component': RegistrationFormComponent,
    }
}

</script>
<style scoped>
</style>
