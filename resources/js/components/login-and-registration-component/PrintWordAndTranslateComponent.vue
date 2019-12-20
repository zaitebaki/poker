<template>
    <div class="uk-flex uk-flex-column">

        <div>
            <p class="uk-text uk-text-center uk-text-lead ft-h-text-print">Вдумчиво напечатай слово и перевод!</p>
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

        <div class="uk-margin-small-top uk-align-center ft-inp-div">            
            <p id="mask-text" class="ft-text-pr uk-text-center">«
            	<span id="mask-text-span">
                <span class="ft-inp-text-green ft-text-pr"> {{ getPrintedText }}</span><span class="ft-inp-text-next-smb ft-text-pr">{{ getCurSmb }}</span><span class="ft-inp-text-left ft-text-pr">{{ getPrintedLeft }}</span>
                </span>
            »</p>
			
			<div class="uk-align-center">
            	<p class="uk-text-center mask-text-p"><input type="text" id="mask-text-input" v-focus v-model="inp_text" class="ft-inp-text" :class="{'ft-inp-text-error': isInputError}" :maxlength="this.mask_text.length" v-on:keyup="check($event)" placeholder="" autocomplete="off" autofocus="autofocus" spellcheck="false" autocorrect="off" autocapitalize="off" onpaste="return false;" ondrag="return false;" ondrop="return false;"></p>
            	<p class="uk-text uk-text-danger ft-error-text" v-if="isInputError">Ошибка ввода!</p>
            </div>
        </div>
        
        <!-- текст напечатан -->
        <div v-if="finish">
            <p class="uk-text uk-text-success uk-text-center ft-info-text-answer">Задание выполнено!</p>
                <p class="uk-text-center">
                    <button
                        class="btn btn-warning ft-btn"
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
            isInputError: false,
            inp_text: '',
            mask_text: this.curWord.original + " " + this.curWord.translation,
            cur_smb_num: 0,
            finish: false
        }

    },
    mounted() {
        let newW = $("#mask-text-span").width();
        $("#mask-text-input").width(newW + 5);
    },
    methods: {
        check($event) {
            this.isInputError = !this.mask_text.startsWith(this.inp_text);

            let i = 0;
            for (i = 0; i < this.inp_text.length; i++) {

                if (!(this.inp_text[i] == this.mask_text[i])) {
                    this.cur_smb_num = 0;
                    break;
                }
            }
            this.cur_smb_num = i;

            // текст напечатан
            if(this.inp_text == this.mask_text) {
                this.finish = true;
            }
        },
    },
    computed: {
        getCurSmb() {
            return this.mask_text[this.cur_smb_num];
        },

        getPrintedText() {
            return this.mask_text.substring(0, this.cur_smb_num);
        },

        getPrintedLeft() {
            return this.mask_text.substring(this.cur_smb_num + 1);
        }
    },
    directives: {
        focus: {
            // определение директивы
            inserted: function(el) {
                el.focus()
            }
        }
    }
}

</script>
<style scoped>

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

.ft-sound-div {
    margin-right: 1rem;
    cursor: pointer;
}

.ft-trans {
    font-size: 1.3rem;
}

.ft-trans-rus {
    font-size: 0.9rem;
}

.ft-inp-text {
    font-family: 'Fira Mono', monospace;
    padding: 0.3rem 0.7rem;
    color: #38c172;
    font-size: 1.5rem;
    border: 1px solid #38c172;
}

.ft-inp-div {
    margin-bottom: 0;
}

.ft-h-text-print {
    font-size: 1.2rem;
    color: #38c172;
    background-color: #DDFDE2;
    padding: 0 6rem;
}

.ft-main-text-print {
    font-size: 1.6rem;
    line-height: 1;
}

.ft-div-star {
    margin-top: 1.1rem;
    margin-bottom: 0.7rem;
}

.mask-text-p {
	margin-bottom: 0.4rem;
}

.ft-error-text {
	font-size: 0.8rem;
	line-height: 1;
	margin: 0;
	margin-left: 0.8rem;
}

.ft-print-text {
	font-family: 'Fira Mono', monospace;
	font-size: 1rem;
	color: white;
	background-color: rgb(30, 135, 240);
	width: auto;

}

.ft-left-text {
   font-size: 0.8rem; 
   padding-right: 2rem;
}

.ft-inp-text-error {
    color: red;
 }

.ft-text-pr {
    font-family: 'Fira Mono', monospace;
    font-size: 1.5rem;
    color: #38c172;
}

.ft-inp-text-next-smb {
    background-color: #242424;
    ;
}

.ft-inp-text-green {
    background-color: #DDFDE2;
}

.ft-inp-text-left {
    background-color: #F0F0F0;
}

</style>
