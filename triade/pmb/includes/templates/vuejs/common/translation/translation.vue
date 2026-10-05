<template>
	<span>
		<button class="bouton" type="button" @click="toggleTranslation()">
			<img :src="images.get('translate.png')" 
			:alt="messages.get('translation', 'translations')" 
			:title="messages.get('translation', 'translations')"/>
		</button>
		<!-- Affichage de la fenetre de traduction -->
		<div :id="nodeId" class="row translations" v-if="showTranslation && translationComponent">
			<template v-for="language in translationManager.languages">
				<label class="etiquette" :for="language.code + '_' + nodeName">{{language.label}}</label>
				<component 
				:is="translationComponent" 
				:nodeName="nodeName" 
				:attributes="attributes" 
				:data="translatedData"
				:code="language.code"
				@input="$emit('input', $event)"
				ref="translationComponent"></component> 
			</template>
		</div>
	</span>
</template>

<script>
	import translationText from "./translationText.vue";
	import translationTextarea from "./translationTextarea.vue";
    import TranslationManager from "@/common/helper/TranslationManager.js";

	export default {
	    props: ["nodeName", "attributes", "type", "data"],
		components: {
			translationText,
			translationTextarea
		},
	    data: function () {
	        return {
                nodeId : "transalations_" + this.nodeName,
				showTranslation : false,
				translatedData : {},
                translationManager : new TranslationManager(),
	        }
	    },
		computed: {
            /**
             * Retourne le composant de tradicton
             * @return {string}
             */
            translationComponent: function() {
                switch (this.type) {
                    case 'text':
                        return 'translation-text';
					case 'textarea':
						return 'translation-textarea';
                    default:
                        return '';
                }
            }
        },
	    created: function() {
			if (!(this.data instanceof Array)) {
				this.$set(this, 'translatedData', this.data);
			}
	    },
		mounted : function() {
		},
	    methods: {
			/**
			 * Affiche/Cache la fenetre de traduction
			 */
			toggleTranslation: function() {
				this.showTranslation = !this.showTranslation;
			},
	    }
	}
</script>