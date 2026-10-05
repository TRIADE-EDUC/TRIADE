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
            <h3>{{ messages.get('dsi', 'view_wysiwyg_input_image') }}</h3>
            <div v-if="block.content == '' || block.content.startsWith('data:')" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="image">{{ messages.get('dsi', 'view_wysiwyg_input_file') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input v-if="block.content == ''" @change="changeImage" type="file"
                            id="image" name="image"
                            accept="image/png, image/jpeg" />
                        <div v-else class="wysiwyg-bg-image-preview">
                            <img width="48" height="48" :src="block.content" alt="" />
                        </div>
                        <button style="cursor: pointer;" v-if="block.content != ''" type="button" @click="block.content = ''">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="! block.content.startsWith('data:')" class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="content">{{ messages.get('dsi', 'view_wysiwyg_input_url') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input name="content" type="text" v-model.lazy="block.content" />
                        <button v-if="block.content != ''" style="cursor: pointer;" class="right" type="button" @click="block.content = ''">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                        <button v-else 
                            name="wysiwyg-galery-insert-img" 
                            id="wysiwyg-galery-insert-img" 
                            type="button" class="bouton" 
                            :title="messages.get('dsi', 'dsi_image_collection_insert_label')" 
                            @click="openImageModal">

                            <i class="fa fa-picture-o" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="alt">{{ messages.get('dsi', 'view_wysiwyg_input_image_alt') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input name="alt" type="text" v-model="block.alt" />
                    </div>
                </div>
            </div>
            <div class="dsi-form-group dsi-form-wysiwyg">
                <label class="etiquette" for="redirect">{{ messages.get('dsi', 'dsi_wysiwyg_image_redirect') }}</label>
                <div class="dsi-form-group-content">
                    <div class="dsi-form-group-line">
                        <input name="redirect" type="url" v-model="block.redirect" />
                    </div>
                </div>
            </div>
            <multimedia-input-form :block="block" element-type="image"></multimedia-input-form>
        </div>
        <modalImageInsert ref="modalImageInsert" @insert="insertImage"></modalImageInsert>
    </div>
</template>

<script>
import multimediaInputForm from './multimediaInputForm.vue';
import modalImageInsert from "@dsi/components/modalImageInsert.vue";

export default {
    name : "imageInputForm",
    props : ['block'],
    components : {
        multimediaInputForm,
        modalImageInsert
    },
    mounted: function() {
        if (!this.block.name) {
            this.$set(this.block, "name", "");
        }
    },
    methods : {
        changeImage(event) {
            let files = event.target.files || event.dataTransfer.files;
            if (!files.length) return;

            const maxKo = this.Const.views.blockImgMaxSize; // 200 Ko
            const maxAllowedSize = maxKo * 1024;

            if (files[0].size > maxAllowedSize) {
                event.target.value = ''
                alert(`${this.messages.get('dsi', 'view_form_image_max_size')} (${maxKo}Ko maximum)`);

                return;
            }

            this.createImage(files[0]);
        },
        createImage(file) {
            var image = new Image();
            var reader = new FileReader();

            reader.onload = (e) => {
                image = e.target.result;
                this.$set(this.block, "content", image);
            };
            reader.readAsDataURL(file);
        },
        openImageModal: function() {
            this.$refs.modalImageInsert.show();
        },
        insertImage: function(image) {
            this.$set(this.block, "content", image.url);

            setTimeout(() => {
                this.$set(this.block, "alt", image.alt);
                this.$set(this.block, "imgHeight", image.height + "px");
                this.$set(this.block, "imgWidth", image.width + "px");
                this.$set(this.block.style.image, "width", image.width + "px");
                this.$set(this.block.style.image, "height", image.height + "px");
                this.$set(this.block, "name", image.name);
                this.$set(this.block, "keepRatio", "0");
            }, 250);

            this.$refs.modalImageInsert.close();
        }
    }
}
</script>