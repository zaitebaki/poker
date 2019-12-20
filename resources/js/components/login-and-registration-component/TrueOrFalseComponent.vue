<template>
    <div class="uk-flex uk-flex-column">
        <div>
            <p class="uk-text uk-text-center uk-text-lead ft-h-text">Верно ли указан перевод слова?</p>
        </div>
        <div class="uk-text-center ft-div-star">
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
        </div>
        <div class="uk-flex uk-flex-center">
            <div class="uk-flex-column uk-flex-right">
                <div class="uk-flex uk-flex-middle uk-flex-right">
                    <div class="ft-sound-div">
                        <img src="/svg/training/sound.svg" style="width: 1.9rem; height: 1.9rem;">
                    </div>
                    <div class="uk-flex-column uk-flex-right">
                        <div>
                            <p class="uk-text-center uk-text-right text-primary ft-main-text-print">{{ curWord[0].original }}</p>
                        </div>
                        <div>
                            <progress class="uk-progress ft-orig-prog1" value="4" max="10"></progress>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="ft-trans-p"> <span class="text-muted ft-trans uk-margin-remove">[{{curWord[0].transcription}}]</span>
                        <span class="text-muted ft-trans-rus" v-html="curWord[0].transcription_rus_accent"></span></p>
                </div>
            </div>
            <div class="uk-margin-small-left uk-margin-small-right">
                <p class="uk-text-center text-primary ft-main-text-print">—</p>
            </div>
            <div class="uk-position-relative">
                <p class="uk-text text-primary ft-main-text-print "
					:class="{'text-primary ft-random-translation': answer == 'none',
							 'text-success': (answer != 'none')}"
                >{{ getRandTranslation }}</p>

                <div class="uk-position-absolute ft-abs-div"
                	 v-if="(answer != 'none') && !coin">
                	<p class="uk-text uk-text-muted">{{ curWord[1].translation }}</p>
                </div>
            </div>
        </div>
        <!-- первое сообщение -->
<!--         <div class="ft-q-text" v-if="answer == 'none'">
            <p class="uk-text uk-text-primary uk-text-center ft-info-text"><span class="ft-print-text">Верно ли указан перевод слова?</span></p>
        </div> -->
        <!-- первое сообщение -->
        <div class="uk-flex uk-flex-column uk-align-center" v-if="answer == 'none'">
            <div class="uk-align-center uk-margin-remove">
                <button type="button" class="btn btn-success ft-btn-tf" @click="onAnswer('yes')">Верно -)</span></button>
            </div>
            <div class="uk-align-center uk-margin-remove">
                <button type="button" class="btn btn-danger ft-btn-tf" @click="onAnswer('no')">Неверно -(</span></button>
            </div>
            <div class="uk-align-center uk-margin-remove ft-last-button-div">
                <button type="button" class="btn btn-dark ft-btn-last" @click="onAnswer('unknown')">Не знаю :(</span></button>
            </div>
        </div>

        <!-- перевод указан верно, ответ 'верно' -->
        <div v-if="(answer == 'yes' || answer == 'unknown') && coin">
            <p class="uk-text uk-text-success uk-text-center ft-info-text-answer">
                <span v-if="answer == 'yes'">Правильно!</span>
                Перевод для слова указан верно
                <span uk-icon="icon: happy; ratio: 1.3"></span>
                <br>
                <button
                	class="btn btn-warning ft-btn-margin-top"
                	@click="$emit('nextWorkout')">
                Далее
                </button>
            </p>
        </div>

        <!-- перевод указан верно, ответ 'неверно' -->
        <div v-if="(answer == 'no') && coin">
            <p class="uk-text uk-text-warning uk-text-center ft-info-text-answer">
                Ошибка! Перевод для слова указан
                <span class="uk-text-success uk-text-bold">ВЕРНО!</span>
                <br>
                <button
                	class="btn btn-warning ft-btn-margin-top"
                	@click="$emit('nextWorkout')">
                Далее
                </button>
            </p>
        </div>

        <!-- перевод указан неверно, ответ 'верно' -->
        <div v-if="(answer == 'yes') && !coin">
            <p class="uk-text uk-text-warning uk-text-center ft-info-text-answer">
                Ошибка! Перевод для слова указан
                <span class="uk-text-bold">НЕВЕРНО!
                </span>
            </p>
            <p class="uk-text uk-text-success uk-text-center ft-info-text-answer">Правильный перевод — 
               <span
                   class="uk-text-bold uk-text-uppercase">
               «{{ curWord[0].translation }}»
               </span>
	           <br>
	           <button
	           	class="btn btn-warning ft-btn-margin-top"
	           	@click="$emit('nextWorkout')">
	           Далее
	           </button>
            </p>
        </div>

        <!-- перевод указан неверно, ответ 'неверно' -->
        <div v-if="(answer == 'no' || answer == 'unknown') && !coin">
            <p class="uk-text uk-text-success uk-text-center ft-info-text-answer">
                <span v-if="answer == 'no'">Правильно!</span>
                Перевод для слова указан
                <span class="uk-text-warning uk-text-bold">НЕВЕРНО!</span>
  			</p>
             <p class="uk-text uk-text-success uk-text-center uk-margin-remove ft-info-text-answer">Правильный перевод — 
	              <span
	                  class="uk-text-bold uk-text-uppercase">
	              «{{ curWord[0].translation }}»
	              </span>
	              <br>
	              <button
	              	class="btn btn-warning ft-btn-margin-top"
	              	@click="$emit('nextWorkout')">
	              Далее
	              </button>
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
        curWord: Array
    },

    data() {
        return {
            answer: 'none',
            coin: null
        }
    },

    methods: {
    	onAnswer(ans) {
            this.answer = ans;
    	}
    },

    computed: {
        getRandTranslation() {
            if(this.answer == 'none') {
	            this.coin = coinToss();
	            return this.coin ? this.curWord[0].translation : this.curWord[1].translation;
	        }
	        else return this.curWord[0].translation;
        }
    }
}

function coinToss() {
    return (Math.floor(Math.random() * 2) === 0);
}

</script>
<style scoped>
.ft-abs-div {
	top: -1.5rem;
	left: 0;

}

.ft-abs-div p {
	text-decoration: line-through;
	font-size: 1.5rem;
}

.ft-trans-p {
    margin-right: -10%;
    text-align: right;
}

.ft-btn-margin-top {
	margin-top: 2rem;
}

.ft-random-translation {
    background-color: yellow;
}

.ft-q-text {
    margin: 1rem 0 0 0;
}

.ft-btn-last {
    margin-top: 1rem;
    width: 10rem;
}

.ft-info-text {
    font-size: 1.4rem;
    margin-bottom: 0;

}

.ft-info-text-answer {
    font-size: 1rem;
}

.ft-btn-tf {
    width: 10rem;
    margin: 0 0 0.5rem 0;
}

.ft-h-text {
    font-size: 1.3rem;
    color: #38c172;
    background-color: #DDFDE2;
}

.ft-but-div {
    margin: 0 auto;
}

.ft-left-text {
    font-size: 0.8rem;
    padding-right: 2rem;
}

.ft-orig-prog1 {
    height: 3px;
    margin-bottom: 0.5rem;
}

.ft-main-text-print {
    font-size: 1.6rem;
    margin-bottom: 0;
}

.ft-div-star {
    margin-top: 1.1rem;
    margin-bottom: 0.7rem;
}

.ft-trans {
    font-size: 1.3rem;
}

.ft-trans-rus {
    font-size: 0.9rem;
}

.ft-sound-div {
    margin-right: 1rem;
    cursor: pointer;
}

</style>
