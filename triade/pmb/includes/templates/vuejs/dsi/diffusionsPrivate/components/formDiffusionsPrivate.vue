<template>
    <div class="dsi-form-diffusion-private-container">
		<form action="#" method="POST" @submit.prevent="save" class="dsi-form-diffusion-private">
            <div class="dsi-form-group">
				<label class="etiquette" for="diffusionModel">{{ messages.get('dsi', 'dsi_private_form_diffusion_model') }}</label>
				<div class="dsi-form-group-content">
                    <select required v-model="formData.diffusionModel" @change="resetForm">
                        <option value="" disabled>{{ messages.get('dsi', 'dsi_private_form_diffusion_model_default_value') }}</option>
                        <option v-for="(diffusion, i) in diffusions" :key="i" :value="diffusion.id">{{diffusion.name}}</option>
                    </select>
				</div>
			</div>
            <form-items-diffusion-private v-if="formData.diffusionModel" :form-data="formData"></form-items-diffusion-private>
             <div class="dsi-form-group">
				<label class="etiquette" for="nbMaxResults">{{ messages.get("dsi", "dsi_private_nb_max_results") }}</label>
                <div class="dsi-form-group-content">
                    <input type="number" v-model="formData.nbMaxResults" min="0" />
                </div>
             </div>
            <div class="left">
                <input type="submit" class="bouton" :value="messages.get('common', 'submit')">
            </div>
        </form>
    </div>
</template>

<script>
import formItemsDiffusionPrivate from './formItemsDiffusionPrivate.vue';

export default {
    name : "formDiffusionsPrivate",
    props : ["diffusions", "formData"],
    components : {
        formItemsDiffusionPrivate
    },
    methods : {
        save : async function() {
            if(! this.formData.selectedItem) {
                this.notif.error(this.messages.get("dsi", "form_diffusions_private_no_item"));
                return;
            }
            let response = await this.ws.post("diffusionsPrivate", "save", this.formData);
            if(response.error) {
                this.notif.error(reponse.errorMessage);
            } else {
                this.notif.info(this.messages.get("dsi", "dsi_private_form_saved"));
            }
        },
        resetForm : function() {
            this.formData.selectedItem = 0;
            this.formData.nbMaxResults = 0;
        }
    }
}
</script>