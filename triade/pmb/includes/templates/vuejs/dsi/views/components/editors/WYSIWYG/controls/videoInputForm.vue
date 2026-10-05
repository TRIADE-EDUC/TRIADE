<template>
     <div>
        <div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="wysiwyg-placement-direction">
                    {{ messages.get('dsi', 'diffusion_name') }}
                </label>
                <div class="dsi-form-group-content">
                    <input type="text" name="name" v-model="block.name" />
                </div>
            </div>
        </div>
        <div>
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_video') }}</h3>
            <div v-if="block.content.value == ''" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="video">{{ messages.get('dsi', 'view_wysiwyg_input_file') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input v-if="block.content.value == ''" @change="changeVideo" type="file"
                            id="video" name="video"
                            accept="video/mp4" />
                            <div v-else class="wysiwyg-bg-video-preview">
                                <!-- <img width="48" height="48" :src="block.content" alt="" /> -->
                            </div>
                            <button style="cursor: pointer;" v-if="block.content.value != ''" type="button" @click="block.content.value = ''">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_video_autoplay') }}</label>
                    <div class="dsi-form-group-content">
                        <div class="dsi-form-group-line">
                            <input v-model="block.video.autoplay" type="checkbox" />
                        </div>
                    </div>
                </div>
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_video_muted') }}</label>
                    <div class="dsi-form-group-content">
                        <div class="dsi-form-group-line">
                            <input v-model="block.video.muted" type="checkbox" />
                        </div>
                    </div>
                </div>
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_video_controls') }}</label>
                    <div class="dsi-form-group-content">
                        <div class="dsi-form-group-line">
                            <input v-model="block.video.controls" type="checkbox" />
                        </div>
                    </div>
                </div>
                <div class="dsi-form-group dsi-form-wysiwyg">
                    <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_input_video_loop') }}</label>
                    <div class="dsi-form-group-content">
                        <div class="dsi-form-group-line">
                            <input v-model="block.video.loop" type="checkbox" />
                        </div>
                    </div>
                </div>
                <multimedia-input-form :block="block" element-type="video"></multimedia-input-form>
            </div>
        </div>
    </div>
</template>

<script>
import multimediaInputForm from './multimediaInputForm.vue';
export default {
    name : "videoInputForm",
    props : ['block'],
    components : {
        multimediaInputForm
    },
    mounted: function() {
        if (!this.block.name) {
            this.$set(this.block, "name", "");
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
        }
    }
}
</script>