<template>
<div>
    <p>{{ statusMessage }}</p>
</div>
</template>

<script>
export default {
    props: {
        // invitationText: String,
        // formRoute: String,
        // formButtonCaption: String,
        statusMessage: String
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
}
</script>