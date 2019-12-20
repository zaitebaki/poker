<template>
    <div class="uk-flex uk-flex-column">

    	<div>
    	    <p class="uk-text uk-text-center uk-text-lead ft-h-text">Выбери правильный перевод!</p>
    	</div>

        <div class="uk-flex uk-flex-middle uk-margin-small-top">
            <div class="uk-flex-1">
				<div class="uk-flex uk-flex-center">
				    <div class="uk-flex-column">

				    	<div class="uk-text-center">
				    	    <span uk-icon="icon: star; ratio: 0.9"></span>
				    	    <span uk-icon="icon: star; ratio: 0.9"></span>
				    	    <span uk-icon="icon: star; ratio: 0.9"></span>
				    	    <span uk-icon="icon: star; ratio: 0.9"></span>
				    	    <span uk-icon="icon: star; ratio: 0.9"></span>
				    	</div>

                        <div class="uk-flex uk-flex-middle uk-flex-center">
                            
                            <div class="ft-sound-div">
                                <img src="/svg/training/sound.svg" style="width: 2rem; height: 2rem;">
                            </div>
    
                            <div class="uk-flex-column">
                                <div>
                                    <p class="uk-text-center text-primary ft-main-text ft-margin-custom">{{ curWord[0].original }}</p>
                                </div>
                                <div>
                                    <progress class="uk-progress ft-orig-prog3" value="4" max="10"></progress>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p> <span class="text-muted ft-trans-custom uk-margin-remove">[{{curWord[0].transcription}}]</span>
                                <span class="text-muted ft-trans-rus-custom" v-html="curWord[0].transcription_rus_accent"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-flex-1">
				<div class="uk-flex uk-flex-column">
					<div class="uk-align-left">
                        <div v-for="index in randArr">
                            <button
                                class="btn ft-btn-2"
                                @click="onAnswer(curWord[index].original, index)"
                                :class="stateClassesArr[index]"
                                >
                            {{ curWord[index].translation }}
                            </button>
                        </div>
                        <div>
                            <button
                                class="btn ft-btn-2-1"
                                @click="onAnswerMainButton"
                                :class="isUnknown ? 'btn-warning' : 'btn-light'">
                                {{ mainButtonText }}
                            </button>
                        </div>
	            	</div>
	            </div>
            </div>
        </div>

        <!-- информационный блок -->
        <div>
            <!-- первое собщение -->
            <p
                class="uk-text uk-text-primary uk-text-center ft-info-text"
                v-if="!isUnknown">
                Выбери перевод слова
                <span
                    class="uk-text-bold">
                {{ curWord[0].original }}
                </span>
            </p>
            
            <!-- правильный ответ -->
            <p
                class="uk-text uk-text-success uk-text-center ft-info-text"
                v-if="answer == 'yes'">
                Правильный ответ!
                <span uk-icon="icon: happy; ratio: 1.7"></span>
                <br>
                Жми «Далее»
            </p>

            <!-- неправильный ответ -->
            <p
                class="uk-text uk-text-warning uk-text-center ft-info-text"
                v-if="answer == 'no'">
                Неверно! Правильный ответ — 
                <span
                    class="uk-text-bold">
                «{{ curWord[0].original }}»
                </span>
            </p>
        </div>
        <hr>
        <div>
            <p class="uk-text uk-text-right ft-left-text"><a class="uk-link-muted" @click="$emit('nextWorkout')">пропустить</a></p>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        curWord: Array,
    },

    data() {
        return {
            randArr: getRandArr(this.curWord.length),
            stateClassesArr: getStateClassesArr(this.curWord.length, "btn-primary"),
            answer: 'none',
            // mainButton
            isUnknown: false,
            mainButtonText: "Не знаю:("
        }
    },

    methods: {
    	onAnswer(original, index) {

            if(this.isUnknown) return;

            // правильный ответ
            if(original == this.curWord[0].original) {
                this.$set(this.stateClassesArr, index, "btn-success");
                this.answer = "yes"
            }
            // неправильный ответ
            else {
                this.$set(this.stateClassesArr, index, "btn-danger");
                this.$set(this.stateClassesArr, 0, "btn-success");
                this.answer = "no"
            }
            this.mainButtonText = "Далее";
            this.isUnknown = true;
    	},

        // главная кнопка
        onAnswerMainButton() {

            // клик на кнопку "не знаю"
            if(!this.isUnknown) {
                this.$set(this.stateClassesArr, 0, "btn-success");
                this.mainButtonText = "Далее";
                this.isUnknown = true;
            }
            // следующее упражнение
            else {
                this.$emit('nextWorkout');
            }            
        }
    }
}

function getStateClassesArr(len, class_name) {
    let dArr = [];

    for (let i = 0; i < len; i++) {
        dArr[i] = class_name;
    }
    return dArr;
}

function getRandArr(len) {
    let dArr = [];

    for (let i = 0; i < len; i++) {
        dArr[i] = i;
    }
    return dArr.sort(compareRandom);
}

function compareRandom(a, b) {
  return Math.random() - 0.5;
}

</script>
<style scoped>

.ft-h-text {
    font-size: 1.3rem;
    color: #38c172;
    background-color: #DDFDE2;
}

.ft-main-text {
    font-size: 1.8rem;
}

.ft-info-text {
    font-size: 1rem;
}

.ft-trans {
    font-size: 1.5rem;
}

.ft-btn-2 {
    width: 80%;
    margin: 0 0 0.5rem 0;
}

.ft-btn-target {
    border: 1px solid black;
}

.ft-btn-2-1 {
    width: 80%;
    margin-top: 1rem;
}

.ft-sound-div {
    margin-right: 1.1rem;
    margin-left: -3.1rem;
    cursor: pointer;
}

.ft-orig-prog3 {
    height: 3px;
}

.ft-margin-custom {
    margin-bottom: 0;
}

.ft-left-text {
   font-size: 0.8rem; 
   padding-right: 2rem;
}

</style>