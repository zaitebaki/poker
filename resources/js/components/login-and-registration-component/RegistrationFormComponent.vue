<template>
    <form id="regForm" class="uk-align-center" :action="formRoute" method="POST">
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="typeForm" value="registration">
        <h3 class="uk-card-title">{{ propsArray.formCaption }}</h3>
        <fieldset class="uk-fieldset">
            <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                <span class=" uk-form-icon" uk-icon="icon: happy"></span>
                <input id="name" class="uk-input" type="text" name="name" :value="oldName" required autofocus
                    :placeholder="propsArray.inputNameCaption">
            </div>
            <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                <span class=" uk-form-icon" uk-icon="icon: user"></span>
                <input id="reg-login" class="uk-input" type="text" name="reg-login" :value="oldRegLogin" required
                    :placeholder="propsArray.inputLoginCaption">
            </div>
            <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                <span class=" uk-form-icon" uk-icon="icon: lock"></span>
                <input class="uk-input uk-width-1-1" type="password" name="password"
                    :placeholder="propsArray.inputPasswordCaption" required>
            </div>

            <button class="uk-button uk-width-1-1" type="submit" form="regForm">{{ propsArray.formButtonCaption }}</button>

            <ul>
                <li class="uk-text-danger" v-for="(error, index) in oldErrors" v-bind:key="index">
                    {{ error[0] }}
                </li>
            </ul>
            
            <label>
                <p class="uk-text-center uk-margin-medium-top">{{ propsArray.formDescription }}
                    <a @click="$emit('enterLogin')">{{ propsArray.formOtherAction }}</a>
                </p>
            </label>
        </fieldset>
    </form>
</template>

<script>

export default {
    props: {
        propsArray: Object,
        formRoute: String,
        oldName: String,
        oldRegLogin: String,
        oldErrors: Object
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
}
	
</script>

<style scoped>
</style>