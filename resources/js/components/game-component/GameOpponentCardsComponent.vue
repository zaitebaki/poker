<template>
<div class="uk-alert-primary" uk-alert>
    <p>{{ invitationText }}</p>
    <form id="startGameForm" :action="formRoute" method="POST">
        <button
            class="uk-button uk-button-secondary uk-button-small"
            type="submit"
            form="startGameForm">
            {{ formButtonCaption }}
        </button>
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="isInvitatationForm" value="true">
    </form>
</div>
</template>

<script>
export default {
    props: {
        invitationText: String,
        formRoute: String,
        formButtonCaption: String
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
}
</script>