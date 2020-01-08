<template>
<div>
    <p uk-margin>
        <button v-if="isActiveButton('startGame')" class="uk-button uk-button-primary" v-on:click="startGame()">{{ buttonsCaptions.startButton }}</button>
        <button
            v-if="isActiveButton('changeCards')"
            class="uk-button uk-button-secondary"
            v-on:click="changeCards()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.changeCards }}
        </button>
        <button
            v-if="isActiveButton('notChange')"
            class="uk-button uk-button-danger"
            v-on:click="changeCards()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.notChange }}
        </button>

        <button v-if="isActiveButton('addMoney')" class="uk-button uk-button-primary" v-on:click="addMoney()">{{ buttonsCaptions.addMoney }}</button>
        <button v-if="isActiveButton('noMoney')" class="uk-button uk-button-secondary" v-on:click="noMoney()">{{ buttonsCaptions.noMoney }}</button>
    </p>
</div>
</template>

<script>
export default {
    props: {
        buttonsCaptions: Object,
        buttons: Array,
        user: Object,
        activeCardsStorage: Array,
        indicatorStatus: String
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    mounted() {
        console.log(this.activeCardsStorage);
    },

    methods: {
        
        // инициировать раздачу карт
        startGame() {
            axios.post('/game/room/1', { initAction: 'startGame', roomName: 'room_1'}).then( (response) => {
                console.log(response.data.gameParameters);
                this.$emit('update:parameters', response.data.gameParameters);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },
        
        // поменять карты
        changeCards() {
            console.log(this.activeCardsStorage);

            if (this.isNotChoosingCardsForChanging()) { return; }
            axios.post('/game/room/1', {
                initAction: 'changeCards',
                roomName: 'room_1',
                cardsIndexForChange: this.getcardsIndexForChange()
                }).
            then( (response) => {
                console.log(response.data.gameParameters);
                this.$emit('update:parameters', response.data.gameParameters);
                this.$root.$emit('clean:cards:classes');
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // проверить есть ли карты для замены
        isNotChoosingCardsForChanging() {
            for (let i = 0; i < this.activeCardsStorage.length; i++) {
                if(this.activeCardsStorage[i] === false) return false;
            }
            return true;
        },

        // вернуть индекс карты для замены
        getcardsIndexForChange() {
            
            let indexArray = [];
            this.activeCardsStorage.forEach(function(item, i, arr) {
                if(item === false) {
                    indexArray.push(i);
                }
            });

            if(indexArray.length === 0) return false;  
            return indexArray.join(',');
        },

        isActiveButton(nameButton) {
            if (this.buttons && this.buttons.indexOf(nameButton) !== -1)
                return true;
            return false;
        },
    }
}
</script>