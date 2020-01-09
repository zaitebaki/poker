<template>
<div>
    <p uk-margin>
        <button
            v-if="isActiveButton('startGame')"
            class="uk-button uk-button-primary"
            v-on:click="startGame()">
            {{ buttonsCaptions.startButton }}
        </button>
        <button
            v-if="isActiveButton('changeCards')"
            class="uk-button uk-button-secondary"
            v-on:click="changeCards('change')"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.changeCards }}
        </button>
        <button
            v-if="isActiveButton('notChange')"
            class="uk-button uk-button-danger"
            v-on:click="changeCards('no:change')"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.notChange }}
        </button>
        <button
            v-if="isActiveButton('addMoney')"
            class="uk-button uk-button-primary"
            v-on:click="addMoney()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ addMoneyCaption }}
        </button>
        <button
            v-if="isActiveButton('noMoney')"
            class="uk-button uk-button-secondary"
            v-on:click="noMoney()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.noMoney }}
        </button>
        <button
            v-if="isActiveButton('equal')"
            class="uk-button uk-button-secondary"
            v-on:click="equal()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.equal }}
        </button>
        <button
            v-if="isActiveButton('equalAndAdd')"
            class="uk-button uk-button-secondary"
            v-on:click="equalAndAdd()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.equalAndAdd }}
        </button>
        <button
            v-if="isActiveButton('gameOver')"
            class="uk-button uk-button-secondary"
            v-on:click="gameOver()"
            v-bind:disabled="indicatorStatus==='wait'">
            {{ buttonsCaptions.gameOver }}
        </button>
    </p>
    <div style="width:600px">
    <b-form-slider v-if="isActiveButton('addMoney')" ref="ticks" v-model="moneySumForAdd" :step="5" :min="5" :max="100" :ticks="ticks" :ticks-labels="tickLabels"></b-form-slider>
    </div>
</div>
</template>

<script>
import bFormSlider from 'vue-bootstrap-slider/es/form-slider';

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
            money: 5,
            moneySumForAdd: 5,
            ticks: [5, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
            tickLabels: ['5', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100'],
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    computed: {
        addMoneyCaption() {
            const re = /:money/gi;
            return this.buttonsCaptions.addMoney.replace(re, this.moneySumForAdd);
        }
    },
    mounted() {
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
        changeCards(change) {
            if (change !== 'no:change' && this.isNotChoosingCardsForChanging() ) { return; }
            axios.post('/game/room/1', {
                initAction: 'changeCards',
                roomName: 'room_1',
                cardsIndexForChange: this.getcardsIndexForChange(change)
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

        // добавить карты
        addMoney() {

            this.money
            // console.log(this.activeCardsStorage);
            // axios.post('/game/room/1', {
            //     initAction: 'addMoney',
            //     roomName: 'room_1',
            //     }).
            // then( (response) => {

            // }).catch(function (error) {
            //     console.log(error);
            //     alert('Не удалось отправить запрос. Повторите попытку позже.');
            // });
        },

        // проверить есть ли карты для замены
        isNotChoosingCardsForChanging() {
            for (let i = 0; i < this.activeCardsStorage.length; i++) {
                if(this.activeCardsStorage[i] === false) return false;
            }
            return true;
        },

        // вернуть индекс карты для замены
        getcardsIndexForChange(change) {
            
            if(change === 'no:change') return false;
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
        slideStart () {
            console.log('slideStarted')
        },
        slideStop () {
            console.log('slideStopped')
        }
    },
    components: {
        'b-form-slider': bFormSlider
    }
}
</script>