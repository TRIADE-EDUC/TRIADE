<template>
    <div class="form-view-selector-type">
        <div v-for="(element, index) in elements" :key="index" class="form-view-selector-type-element">
            <button type="button" :class="getClassSelected(methodSelected ==  2 ? element.value : element.id)" @click="clickedElement(element)">
                <img :src="getSrcIcon(element.icon)" alt="">
                <span>{{ element.label }}</span>
            </button>
        </div>

        <div v-if="!elements.length" class="form-view-selector-type-element">
            <span>{{ messages.get("dsi", "view_configuration_empty_selector") }}</span>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["elements", "elementSelected", "view", "methodSelected"],
        data: function() {
            return {
                element: this.elementSelected
            }
        },
        methods: {
            /**
             * Gère l'événement de clic sur un élément.
             *
             * @param {Object} element - L'élément qui a été cliqué.
             * @return {void}
             */
            clickedElement: function(element) {
                this.element = element;
                this.$emit("clickedElement", element);
            },

            /**
             * Retourne la classe "selected" si la valeur correspond au type de la vue, sinon retourne une chaîne vide.
             *
             * @param {type} value - La valeur à comparer avec le type de la vue actuel.
             * @return {String} - La classe "selected" ou une chaîne vide.
             */
            getClassSelected: function(value) {
                if(this.element) {
                    switch(this.methodSelected) {
                        case 2:
                            if(this.element.value == value) {
                                return "selected"
                            }
                            break;
                        case 1:
                            if(this.element.id == value) {
                                return "selected"
                            }
                            break;
                        default:
                            break;
                    }
                }
                return "";
            },

            /**
             * Retourne l'icône source en fonction du paramètre icône donné.
             *
             * @param {string} icône - L'icône à vérifier.
             * @return {string} L'icône source ou le chemin vers l'icône.
             */
            getSrcIcon: function(icon) {
                const regex = /^data:image\/(png|jpeg|gif|webp);base64,/;
                if (icon && regex.test(icon)) {
                    return icon;
                }

                return './' + icon;
            }

        }
    };
</script>