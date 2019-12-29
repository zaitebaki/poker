<template>
<div>
    <p>Ставка в начале раунда - 5 Р. Максимальная ставка за 1 раунд торгов - 100 Р.</p>
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