<template>
    <multimedia-input :block="block" :parent="parent" :blockLabels="blockLabels" :blockTypes="blockTypes">
        <template #media>
            <a v-if="block.redirect" :href="block.redirect">
                <img v-if="block.content == ''" id="media-content" src="" :width="block.style.imageWidth" :height="block.style.imageHeight" :alt="block.alt"/>
                <img v-else id="media-content" :src="block.content" :style="block.style.image" :alt="block.alt" />
            </a>
            <img v-else-if="block.content == ''" id="media-content" src="" :width="block.style.imageWidth" :height="block.style.imageHeight" :alt="block.alt"/>
            <img v-else id="media-content" :src="block.content" :style="block.style.image" :alt="block.alt" />
        </template>
    </multimedia-input>
</template>

<script>
import multimediaInput from './multimediaInput.vue';
export default {
    props : ['block', 'blockTypes', 'blockLabels', 'parent'],
    components : {
        multimediaInput
    },
    watch : {
        "block.content" : {
            handler() {
                //Timeout pour que l'image soit chargee
                setTimeout(() => {

                    const img = new Image();
                    img.src = this.block.content;

                    this.$set(this.block, "imgWidth", img.width);
                    this.$set(this.block, "imgHeight", img.height);
                    if(this.block.keepRatio) {
                        this.$set(this.block.style.image, "width", img.width + "px");
                    } else {
                        this.$set(this.block.style.image, "width", img.width + "px");
                        this.$set(this.block.style.image, "height", img.height + "px");
                    }
                }, 200)
            },
            flush : "post"
        }
    },
    mounted : function() {
        if(! this.block.content) {
            this.block.content = "";
        }
        if(! this.block.alt) {
            this.$set(this.block, "alt", "");
        }
        if(! this.block.keepRatio) {
            this.$set(this.block, "keepRatio", true);
        }
        if(! this.block.style) {
            this.$set(this.block, "style", {});
        }
        if(! this.block.style.block) {
            this.$set(this.block.style, "block", {});
        }
        if(! this.block.style.image) {
            this.$set(this.block.style, "image", {});
        }
    }
}
</script>