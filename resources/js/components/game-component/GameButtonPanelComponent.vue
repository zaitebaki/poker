<template>
<div
    class="uk-margin-medium-top uk-margin-medium-bottom"
    :class="{'uk-margin-remove uk-padding-small': isActiveButton('then')}">
    <div class="uk-flex uk-flex-center">
        <p uk-margin class="uk-margin-remove">
            <button
                v-if="isActiveButton('startGame')"
                class="uk-button uk-button-primary"
                v-on:click="startGame()"
                v-bind:disabled="startButtonIndicator">
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
                v-on:click="check()"
                v-bind:disabled="indicatorStatus==='wait'">
                {{ buttonsCaptions.noMoney }}
            </button>
            <button
                class="uk-button uk-button-primary"
                v-if="isActiveButton('equalAndAdd')"
                v-on:click="equalAndAdd()"
                v-bind:disabled="indicatorStatus==='wait'">
                {{ equalAndAddCaption }}
            </button>
            <button
                v-if="isActiveButton('equal')"
                class="uk-button uk-button-primary"
                v-on:click="equal()"
                v-bind:disabled="indicatorStatus==='wait'">
                {{ equalCaption }}
            </button>
            <button
                v-if="isActiveButton('gameOver')"
                class="uk-button uk-button-danger"
                v-on:click="gameOver()"
                v-bind:disabled="indicatorStatus==='wait'">
                {{ buttonsCaptions.gameOver }}
            </button>
            <button
                v-if="isActiveButton('then')"
                class="uk-button uk-button-danger"
                v-on:click="then()"
                v-bind:disabled="newGameButtonIndicator">
                {{ buttonsCaptions.then }}
            </button>
        </p>
    </div>
    <div class="uk-flex uk-flex-center uk-margin-small-top" v-if="!isActiveButton('then')">
        <b-form-slider
            ref="ticks"
            v-if="isActiveButton('addMoney') || isActiveButton('equalAndAdd')"
            v-model="moneySumForAdd"
            :step="5"
            :min="5"
            :max="100"
            :ticks="ticks"
            tooltip="hide"
            :ticks-labels="tickLabels">
        </b-form-slider>
    </div>
</div>
</template>

<script>
import bFormSlider from 'vue-bootstrap-slider/es/form-slider';

export default {
    props: {
        roomUrl: String,
        roomName: String,
        buttonsCaptions: Object,
        buttons: Array,
        user: Object,
        activeCardsStorage: Array,
        indicatorStatus: String,
        money: String,
        addOpponentMoney: String,
        increaseAfterEqualMoney: String,
        startButtonIndicator: Boolean,
        opponentStatusCheck: Boolean,
        newGameButtonIndicator: Boolean
    },
    data() {
        return {
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
        },
        equalAndAddCaption() {
            let re = /:money1/gi;
            let caption = this.buttonsCaptions.equalAndAdd.replace(re, this.getCurrentAddingMoney());
            re = /:money2/gi;
            return caption.replace(re, this.moneySumForAdd);
        },
        equalCaption() {
            const re = /:money/gi;
            return this.buttonsCaptions.equal.replace(re, this.getCurrentAddingMoney());
        }
    },
    mounted() {
        console.log(this.increaseAfterEqualMoney);
    },

    methods: {
        
        // инициировать раздачу карт
        startGame() {
            axios.post(this.roomUrl, { initAction: 'startGame', roomName: this.roomName}).then( (response) => {
                console.log(response.data);
                this.$emit('update:parameters', response.data);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },
        
        // поменять карты
        changeCards(change) {
            console.log(this.activeCardsStorage);
            if (change !== 'no:change' && this.isNotChoosingCardsForChanging() ) { return; }
            axios.post(this.roomUrl, {
                initAction: 'changeCards',
                roomName: this.roomName,
                cardsIndexForChange: this.getcardsIndexForChange(change)
                }).
            then( (response) => {
                console.log(response.data);
                this.$emit('update:parameters', response.data);
                this.$root.$emit('clean:cards:classes');
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // добавить карты
        addMoney() {
            axios.post(this.roomUrl, {
                initAction: 'addMoney',
                roomName: this.roomName,
                money: this.moneySumForAdd
                }).
            then( (response) => {
                this.$emit('update:parameters', response.data);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // чек
        check() {
            // если оппонент уже ответил "чек"
            if (this.opponentStatusCheck === true) {
                console.log('Another check');
                axios.post(this.roomUrl, {
                    initAction: 'opponentCheck',
                    roomName: this.roomName,
                    }).
                then( (response) => {
                    console.log(response.data);
                    this.$emit('update:parameters', response.data);
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            }
            // пользователь говорит "чек" в 1-ый раз
            else {
                axios.post(this.roomUrl, {
                    initAction: 'check',
                    roomName: this.roomName,
                    }).
                then( (response) => {
                    this.$emit('update:parameters', response.data);
                }).catch(function (error) {
                    console.log(error);
                    alert('Не удалось отправить запрос. Повторите попытку позже.');
                });
            }
        },

        // сравянть и добавить
        equalAndAdd() {
            axios.post(this.roomUrl, {
                initAction: 'equalAndAdd',
                roomName: this.roomName,
                moneyequal: this.getCurrentAddingMoney(),
                moneyAdd: this.moneySumForAdd
                }).
            then( (response) => {
                this.$emit('update:parameters', response.data);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // сравнять
        equal() {
            axios.post(this.roomUrl, {
                initAction: 'equal',
                roomName: this.roomName,
                money: this.getCurrentAddingMoney()
                }).
            then( (response) => {
                console.log(response.data);
                this.$emit('update:parameters', response.data);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // сбросить карты
        gameOver() {
            axios.post(this.roomUrl, {
                initAction: 'gameOver',
                roomName: this.roomName,
                money: this.getDropMoney()
                }).
            then( (response) => {
                this.$emit('update:parameters', response.data);
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // начать новую партию
        then() {
            console.log(this.roomName);
            axios.post(this.roomUrl, {
                initAction: 'nextRound',
                roomName: this.roomName,
                }).
            then( (response) => {

                console.log(response.data);
                this.$emit('update:parameters', response.data);
                this.$root.$emit('changed:cards:false');
                
            }).catch(function (error) {
                console.log(error);
                alert('Не удалось отправить запрос. Повторите попытку позже.');
            });
        },

        // проверить есть ли карты для замены
        isNotChoosingCardsForChanging() {
            for (let i = 0; i < this.activeCardsStorage.length; i++) {
                if (this.activeCardsStorage[i] === false) return false;
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

        getCurrentAddingMoney() {
            if (this.increaseAfterEqualMoney !== '0') {
                return this.increaseAfterEqualMoney;
            }
            else {
                return this.addOpponentMoney;
            }
        },
        getDropMoney() {
            return (+(this.money) - +(this.getCurrentAddingMoney()))/2;
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