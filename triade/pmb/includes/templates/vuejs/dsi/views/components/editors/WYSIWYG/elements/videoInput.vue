<template>
    <multimedia-input :block="block" :parent="parent" :blockLabels="blockLabels" :blockTypes="blockTypes">
        <template #media>
            <video :id="'video-document-' + block.id" v-if="block.content.value != ''"
                :style="block.style.video" 
                :controls="block.video.controls" 
                :autoplay="block.video.autoplay" 
                :muted="block.video.muted"
                :loop="block.video.loop"
                :width="block.video.width"
                :height="block.video.height">
                <source :src="block.content.value" :type="block.content.mimetype" />
            </video>
            <i v-else class="fa fa-youtube-play" aria-hidden="true"></i>
        </template>
    </multimedia-input>
</template>

<script>
import multimediaInput from './multimediaInput.vue';
export default {
    props : ['block', 'blockTypes', 'blockLabels', "parent"],
    components : {
        multimediaInput
    },
    watch : {
        "block.content.value" : {
            handler() {
                //Timeout pour que l'image soit chargee
                setTimeout(() => {
                    let video = document.getElementById('video-document-' + this.block.id);
                    if(video === null) {
                        return;
                    }
                    this.$set(this.block, "imgWidth", video.videoWidth);
                    this.$set(this.block, "imgHeight", video.videoHeight);
                    if(this.block.keepRatio) {
                        this.$set(this.block.style.video, "width", video.videoWidth + "px");
                    } else {
                        this.$set(this.block.style.video, "width", video.videoWidth + "px");
                        this.$set(this.block.style.video, "height", video.videoHeight + "px");
                    }
                }, 1000)
            },
            flush : "post"
        }
    },
    created : function() {
        if(! this.block.content) {
            this.block.content = {
                value : "",
                mimetype : "",
            };
        }
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
        if(! this.block.style.video) {
            this.$set(this.block.style, "video", {});
        }
        if(! this.block.video) {
            this.$set(this.block, "video", {});
        }
        if(! this.block.video.autoplay) {
            this.$set(this.block.video, "autoplay", false);
        }
        if(! this.block.video.muted) {
            this.$set(this.block.video, "muted", false);
        }
        if(! this.block.video.controls) {
            this.$set(this.block.video, "controls", false);
        }
        if(! this.block.video.loop) {
            this.$set(this.block.video, "loop", false);
        }
    },
    methods : {
        changeVideo(event) {
            var files = event.target.files || event.dataTransfer.files;
            if (!files.length) return;
            this.createVideo(files[0]);
        },
        createVideo(file) {
            var video = new Image();
            var reader = new FileReader();
            reader.onload = (e) => {
                video = e.target.result;
                this.$set(this.block.content, "value", video);
            };
            this.$set(this.block.content, "mimetype", file.type);
            reader.readAsDataURL(file);
        },
    }
}
</script>