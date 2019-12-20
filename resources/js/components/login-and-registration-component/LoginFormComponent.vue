<template>
    <!-- <form class="uk-align-center" action="{{ route('authencticate') }}" method="POST"> --}} -->
    <form class="uk-align-center" method="POST">
        <!-- {{ csrf_field() }} -->
        <h3 class="uk-card-title">{{ propsArray.formCaption }}</h3>
        <fieldset class="uk-fieldset">
            <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                <span class=" uk-form-icon" uk-icon="icon: user"></span>
                <input id="entry-login" class="uk-input" type="text" name="login" required autofocus
                    :placeholder="propsArray.inputLoginCaption">
            </div>
            <div class="uk-inline uk-width-1-1 uk-margin-bottom">
                <span class=" uk-form-icon" uk-icon="icon: lock"></span>
                <input class="uk-input uk-width-1-1" id="password" type="password" name="password"
                    :placeholder="propsArray.inputPasswordCaption" required>
            </div>

            <button class="uk-button uk-width-1-1" type="submit">{{ propsArray.formButtonCaption }}</button>
            
            <label>
                <p class="uk-text-center uk-margin-medium-top">{{ propsArray.formDescription }}
                    <a @click="$emit('enterRegistration')">{{ propsArray.formOtherAction }}</a>
                </p>
            </label>
        </fieldset>
    </form>
</template>

<script>

export default {
    props: [
        'propsArray'
    ],
}
	
</script>

<style scoped>
</style>