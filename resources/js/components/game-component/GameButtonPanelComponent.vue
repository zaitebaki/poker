<template>
<div>
    <p uk-margin>
        <!-- <button class="uk-button uk-button-default">Default</button> -->
        <button v-if="startGameButtonReady" class="uk-button uk-button-primary">{{ buttonsCaptions.startButton }}</button>

        <!-- <button class="uk-button uk-button-secondary">Secondary</button>
        <button class="uk-button uk-button-danger">Danger</button>
        <button class="uk-button uk-button-text">Text</button>
        <button class="uk-button uk-button-link">Link</button> -->
    </p>
</div>
</template>

<script>
export default {
    props: {
        buttonsCaptions: Object,
        startGameButtonReady: Boolean
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },

    sendInvitation: function(friendLogin) {
        axios.post('/invitation', { srcUserId: this.user.id, dstUserLogin: friendLogin}).then(function (response) {


            if (response.redirect) {
                console.log('redirect'); 
            }
            // if(response.data === 'STATUS_OK') {
            //     console.log('Приглашение успешно отправлено!');
            // }
            // window.location.href = response.data;
            // console.log(response);
        }).catch(function (error) {
            console.log(error);
            alert('Не удалось отправить запрос. Повторите попытку позже.');
        })
    }
}
</script>