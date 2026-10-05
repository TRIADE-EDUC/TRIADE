<template>
    <div>
        <h1 class="title">{{ messages.get('dsi', 'diffusion_private_title') }}</h1>
        <form action="#" method="POST" @submit.prevent="save">
            <div class="dsi-search-equation" v-html="formData.humanQuery"></div>
            <div class="row">
                <div class="dsi-form-group">
                    <label for="diffusionName">{{ messages.get('dsi', 'diffusion_private_name')}}</label>
                    <div class="dsi-form-group-content">
                        <input required name="diffusionName" type="text" v-model="formData.diffusionPrivateName" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="dsi-form-group">
                    <label for="diffusionPeriodicity">{{ messages.get('dsi', 'diffusion_private_periodicity')}}</label>
                    <div class="dsi-form-group-content">
                        <input required type="number" name="diffusionPeriodicity" min="1" v-model="formData.diffusionPrivatePeriodicity" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="dsi-form-group">
                    <label for="diffusionPrivateTime">{{ messages.get('dsi', 'diffusion_private_time')}}</label>
                    <div class="dsi-form-group-content">
                        <input required type="time" name="diffusionPrivateTime" v-model="formData.diffusionPrivateTime" />
                    </div>
                </div>
            </div>
            <input type="submit" class="bouton" :value="messages.get('dsi', 'diffusion_private_save')" />
        </form>
    </div>
</template>

<script>
export default {
    name : 'formDiffusionsPrivate',
    props : ["formData"],
    methods : {
        save : async function() {
            let response = await this.ws.post("diffusionsPrivate", "save", this.formData);
            if(! response.error) {
                document.location = "./empr.php?tab=dsi&lvl=bannette";
            }
        }
    }
}
</script>