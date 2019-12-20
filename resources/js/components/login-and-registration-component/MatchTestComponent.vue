<template>

	<div class="uk-flex uk-flex-column">

		<div>
		    <p class="uk-text uk-text-center uk-text-lead ft-h-text">Установи соответствие между словом и переводом</p>
		</div>

		<!-- первое собщение -->
		<p
		    class="uk-text uk-text-primary uk-text-center ft-info-text"
		    v-if="answer == 'none'">
		    Перетяни слово на его перевод
		</p>
		
		<!-- правильный ответ -->
		<p
		    class="uk-text uk-text-success uk-text-center ft-info-text"
		    v-if="answer == 'yes'">
		    Правильный ответ!
		    <span uk-icon="icon: happy; ratio: 1.7"></span>
		</p>

		<!-- неправильный ответ -->
		<p
		    class="uk-text uk-text-warning uk-text-center ft-info-text"
		    v-if="answer == 'no'">
		    Неверно!
		    {{ curWord[lastDragIndex].original }} ≠ {{ curWord[lastDropIndex].translation }}
		</p>
		
		<!-- Задание выполнено -->
		<div v-if="answer == 'finish'">
			<p class="uk-text uk-text-success uk-text-center ft-info-text">
			    Задание выполнено!
			    <span uk-icon="icon: happy; ratio: 1.7"></span>
			</p>
			<p class="uk-text-center">
			<button
				class="btn btn-warning"
				@click="$emit('nextWorkout')">
			Далее
			</button>
			</p>
			<hr>
		</div>

	    <div class="uk-flex">

			<div class="uk-flex-1">
				<div v-for="index in sequenceArr">
					<drag 
						class="uk-align-center ft-btn-test"
						:transfer-data="{ 
							original: curWord[index].original,
							index: index,
							}"
						:key="index">
						<button class="btn btn-warning">
						{{ curWord[index].original }}
						</button>
					</drag>
				</div>
			</div>

			<div class="uk-flex-1">
				<div v-for="index in randArr">
					<drop 
						class="ft-btn-test uk-align-center"
						:class="{'over': overClasses[index]}"
						:key="index"
						@dragover="toggleOverClass(index, true)"
						@dragleave="toggleOverClass(index, false)"
						@drop="handleDrop({
							original: curWord[index].original,
							index: index
							}, ...arguments)">
						<button
							class="btn btn-dark"
							@click="$emit('nextWorkout')">
						{{ curWord[index].translation }}
						</button>
					</drop>
				</div>
			</div>
	    </div>

	    <template v-for="index in finishArr">

	    	<div class="uk-card">
	    		<div class="uk-text-center">
	    		    <span uk-icon="icon: star; ratio: 0.5"></span>
	    		    <span uk-icon="icon: star; ratio: 0.5"></span>
	    		    <span uk-icon="icon: star; ratio: 0.5"></span>
	    		    <span uk-icon="icon: star; ratio: 0.5"></span>
	    		    <span uk-icon="icon: star; ratio: 0.5"></span>
	    		</div>
	    		<div class="uk-flex uk-flex-center">
					<div class="uk-flex-column">

						<div class="uk-flex uk-flex-middle uk-flex-right">

							<div class="ft-sound-div">
							    <img src="/svg/training/sound.svg" style="width: 1.2rem; height: 1.2rem;">
							</div>

							<div class="uk-flex-column">

								<div>
								    <p class="uk-text-center text-success ft-main-text-print-custom ft-margin-success-card">{{ curWord[index].original }}</p>
								</div>
								<div>
								    <progress class="uk-progress ft-orig-prog2" value="4" max="10"></progress>
								</div>
							</div>
						</div>
					    <div>
					        <p> <span class="text-muted ft-trans-custom uk-margin-remove">[{{curWord[index].transcription}}]</span>
					            <span class="text-muted ft-trans-rus-custom" v-html="curWord[index].transcription_rus_accent"></span></p>
					    </div>
					</div>
					
					<div class="uk-margin-small-left uk-margin-small-right">
					    <p class="uk-text-center text-success ft-main-text-print-custom">—</p>
					</div>

					<div class="uk-margin-small-left">
					    <p class="uk-text text-success ft-main-text-print-custom">{{ curWord[index].translation }}</p>
					</div>
				</div>
			</div>
	    </template>
		
		<hr>
	    <div>
	        <p class="uk-text uk-text-right ft-left-text"><a class="uk-link-muted" @click="$emit('nextWorkout')">пропустить</a></p>
	    </div>
	  </div>
</template>
<script>
import { Drag, Drop } from 'vue-drag-drop';


export default {
	props: {
	    curWord: Array
	},

    data() {
        return {
        	overClasses: getOverClassArr(this.curWord.length),
        	sequenceArr: getSequenceArr(this.curWord.length),
        	randArr: getRandArr(this.curWord.length),
        	answer: 'none',
        	finishArr: [],
        	lastDragIndex: null,
        	lastDropIndex: null
        }
    },

    methods: {
    	handleDrop(original_drop_data, original_drag_data, event) {

    		this.lastDragIndex = original_drag_data.index;
    		this.lastDropIndex = original_drop_data.index;

    		// правильный ответ
    		if(original_drop_data.original == original_drag_data.original) {

    			delElement(this.sequenceArr, original_drag_data.index);
    			delElement(this.randArr, original_drop_data.index);

    			this.finishArr.unshift(original_drag_data.index);
    			this.answer = "yes";

    			// соответствия установлены
    			if(this.sequenceArr.length == 0) {
    				this.answer = "finish";
    				// this.$emit('nextWorkout');
    			}
    		}
    		// неправильный ответ
    		else {
    			this.$set(this.overClasses, original_drop_data.index, false);
    			this.answer = "no";
    		}
    	},

    	// переключение класса при наведении
    	toggleOverClass(index, value) {
    		this.$set(this.overClasses, index, value);
    	},
    },
}

function getOverClassArr(len) {
	let dArr = [];

	for (let i = 0; i < len; i++) {
		dArr.overclass = false;
	}

	return dArr;
}

function getSequenceArr(len) {
	let dArr = [];

	for (let i = 0; i < len; i++) {
		dArr[i] = i;
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

function delElement(arr, val) {

	for (let i = 0; i < arr.length; i++) {
		if(arr[i] == val) {
			arr.splice(i, 1);
			break;
		}
	}
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

.ft-btn-test {
	width: 13em;
	padding: 0;
	margin: 0 auto 10px auto;
}

.ft-btn-test button {
	width: 100%;
	height: 2.8rem;
}

.over button{
	border-color: #aaa;
	background: #ccc;
}

.ft-success-div {
	background-color: green;
}

.ft-main-text-print-custom {
    font-size: 1.1rem;
    line-height: 1;
}

.ft-trans-custom {
	font-size: 1rem;
}

.ft-trans-rus-custom {
	font-size: 0.75rem;
}

.ft-orig-prog2 {
	height: 2px;
	margin-bottom: 0;
}

.ft-margin-success-card {
	margin-bottom: 0.2rem;
}

.ft-div-success-card {
	padding-left: 20%;
}

.ft-left-text {
   font-size: 0.8rem; 
   padding-right: 2rem;
}

.ft-sound-div {
	margin-right: 0.8rem;
	cursor: pointer;

}
</style>