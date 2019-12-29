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
}
    
</script>