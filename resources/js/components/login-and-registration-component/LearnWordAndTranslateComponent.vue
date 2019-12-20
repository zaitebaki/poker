<template>
    <div class="uk-flex uk-flex-column">
        <div>
            <p class="uk-text uk-text-center uk-text-lead ft-h-text">Выучи слово и перевод!</p>
        </div>

        <div class="uk-text-center ft-div-star">
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
            <span uk-icon="icon: star; ratio: 0.9"></span>
        </div>

        <div class="uk-flex uk-flex-center">

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

                    <div>
                        <p> <span class="text-muted ft-trans uk-margin-remove">[{{curWord.transcription}}]</span>
                            <span class="text-muted ft-trans-rus" v-html="curWord.transcription_rus_accent"></span></p>
                    </div>
                </div>        
            <div class="uk-margin-small-left uk-margin-small-right">
                <p class="uk-text-center text-primary ft-main-text-print">—</p>
            </div>
            <div>
                <p class="uk-text text-primary ft-main-text-print">{{ curWord.translation }}</p>
            </div>
        </div>

        <!-- кнопки -->
        <div class="uk-align-center">
            <div>
                <button type="button" class="btn btn-success" @click="$emit('nextWorkout')">Выучил! <span uk-icon="icon: happy" class="uk-icon"></span></button>
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
    
    computed: {
        // геттер вычисляемого значения
        word_translate() {
            return this.curWord.original + " | " + this.curWord.translation;
        }
    }
}

</script>
<style scoped>

.ft-h-text {
    font-size: 1.3rem;
    color: #38c172;
    background-color: #DDFDE2;
}

.ft-main-text-margin {
    margin: 20px 0 5px 0;
}

.ft-orig-prog1 {
    height: 3px;
    margin-bottom: 0.5rem;
}

.ft-trans {
    font-size: 1.3rem;
}

.ft-trans-rus {
    font-size: 0.9rem;
}

.ft-main-text-print {
    font-size: 1.6rem;
    margin-bottom: 0;
}

.ft-div-star {
    margin-top: 1.1rem;
    margin-bottom: 0.7rem;
}

.ft-sound-div {
    margin-right: 1rem;
    cursor: pointer;
}

.ft-left-text {
   font-size: 0.8rem; 
   padding-right: 2rem;
}
</style>
