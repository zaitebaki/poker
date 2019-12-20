<template>
	<div class="uk-flex uk-flex-column">
	    <div>
	        <p class="uk-text uk-text-center uk-text-lead ft-h-text">Знаешь перевод слова?</p>
	    </div>

	    <div class="uk-flex uk-flex-center">
	        <div class="uk-flex-column">

	        	<div class="uk-text-center ft-div-star">
	        	    <span uk-icon="icon: star; ratio: 0.9"></span>
	        	    <span uk-icon="icon: star; ratio: 0.9"></span>
	        	    <span uk-icon="icon: star; ratio: 0.9"></span>
	        	    <span uk-icon="icon: star; ratio: 0.9"></span>
	        	    <span uk-icon="icon: star; ratio: 0.9"></span>
	        	</div>
				
				<!-- слово без перевода -->
	            <div class="uk-flex uk-flex-middle uk-flex-center"
	            	 v-if="answer == 'none'">
	                
	                <div class="ft-sound-div">
	                    <img src="/svg/training/sound.svg" style="width: 2rem; height: 2rem;">
	                </div>
	    
	                <div class="uk-flex-column">
	                    <div>
	                        <p class="uk-text-center text-primary ft-main-text ft-margin-custom">{{ curWord.original }}</p>
	                    </div>
	                    <div>
	                        <progress class="uk-progress ft-orig-prog3" value="4" max="10"></progress>
	                    </div>
	                </div>
	            </div>
		
				<!-- слово с переводом -->
	            <div class="uk-flex uk-flex-center"
						v-if="answer != 'none'">
	                    <div class="uk-flex-column uk-text-center">
	                        
	                        <div class="uk-flex uk-flex-middle uk-flex-right">

	                            <div class="ft-sound-div">
	                                <img src="/svg/training/sound.svg" style="width: 1.9rem; height: 1.9rem;">
	                            </div>

	                            <div class="uk-flex-column">
	                                <div>
	                                    <p class="uk-text-center text-primary ft-main-text-print">{{ curWord.original }}</p>
	                                </div>

	                                <div>
	                                    <progress class="uk-progress ft-orig-prog1" value="4" max="10"></progress>
	                                </div>
	                            </div>
	                  
	                        </div>
	                    </div>        
	                <div class="uk-margin-small-left uk-margin-small-right">
	                    <p class="uk-text-center text-primary ft-main-text-print">—</p>
	                </div>
	                <div>
	                    <p class="uk-text text-primary ft-main-text-print">{{ curWord.translation }}</p>
	                </div>
	            </div>

	            <div>
	                <p> <span class="text-muted ft-trans uk-margin-remove">[{{curWord.transcription}}]</span>
	                    <span class="text-muted ft-trans-rus" v-html="curWord.transcription_rus_accent"></span></p>
	            </div>
	        </div>
	    </div>
	    
		<!-- кнопки -->
		<div class="uk-align-center"
			 v-if="answer == 'none'">
		    <div class="uk-flex uk-flex-column">
		        <div class="uk-margin-small-bottom">
		            <button type="button" class="btn btn-success ft-btn" @click="onAnswer('yes')">Знаю <span uk-icon="icon: happy" class="uk-icon"></span></button>
		            <button type="button" class="btn btn-danger ft-btn"  @click="onAnswer('error')">Не знаю <span uk-icon="icon: happy" class="uk-icon"></span></button>
		        </div>
		    </div>
		</div>

		<!-- кнопки после выбора "я знаю" -->
		<div class="uk-align-center ft-div-margin-hand"
			 v-if="answer == 'yes'">
			 <div>
			 	<p class="uk-text uk-text-success uk-text-center ft-info-text-answer">
					Перевод слова — 
					<span
					    class="uk-text-bold uk-text-uppercase">
					«{{ curWord.translation }}»
					</span>
			 	</p>
			 </div>
		    <div class="uk-flex uk-flex-column">
		        <div class="uk-margin-small-bottom">
		            <button type="button" class="btn btn-success ft-btn" @click="$emit('nextWorkout')">Так и думал(а) <span uk-icon="icon: happy" class="uk-icon"></span></button>
		            <button type="button" class="btn btn-danger ft-btn"  @click="onAnswer('error')">Ошибся(лась) <span uk-icon="icon: happy" class="uk-icon"></span></button>
		        </div>
		    </div>
		</div>


		<!-- кнопки -->
		<div class="uk-align-center"
			 v-if="answer == 'error'">
			 <div>
			 	<p class="uk-text uk-text-success uk-text-center ft-info-text-answer">Выучи слово и его перевод!</p>
			 </div>
		    <div>
		    	<p class="uk-text-center">
		        	<button type="button" class="btn btn-success" @click="$emit('nextWorkout')">Выучил! <span uk-icon="icon: happy" class="uk-icon"></span></button>
		        </p>
		    </div>
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
	        answer: 'none'
	    }
	},

	methods: {
		onAnswer(ans) {
			this.answer = ans;
		}
	}
}
	
</script>

<style scoped>

.ft-div-margin-hand {
	margin: 1.5rem auto;
}

.ft-h-text {
    font-size: 1.3rem;
    color: #38c172;
    background-color: #DDFDE2;
}

.ft-info-text-answer {
    font-size: 1rem;
}

.ft-main-text-print {
    font-size: 1.6rem;
    margin-bottom: 0;
}

.ft-orig-prog1 {
    height: 3px;
    margin-bottom: 0.5rem;
}

.ft-div-star {
    margin-top: 1.1rem;
    margin-bottom: 0.7rem;
}

.ft-btn-know {
    background-color: #2daf58;
    color: white;
}

.ft-btn {
    width: 10rem;
}

.ft-main-text {
    font-size: 1.9rem;
}

.ft-margin-custom {
    margin-bottom: 0;
}

.ft-sound-div {
    margin-right: 1.1rem;
    margin-left: -3.1rem;
    cursor: pointer;
}

.ft-orig-prog3 {
    height: 4px;
}

.ft-trans {
    font-size: 1.5rem;
}

.ft-left-text {
   font-size: 0.8rem;
   padding-right: 2rem;
}

.ft-trans {
    font-size: 1.3rem;
}

.ft-trans-rus {
    font-size: 0.9rem;
}


</style>
