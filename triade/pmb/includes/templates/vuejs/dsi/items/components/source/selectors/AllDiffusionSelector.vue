<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group">
            <label for="diffusionList">Diffusions :</label>
            <div class="dsi-form-group-content">
                <select multiple name="diffusionList" v-model="data.ids">
                    <option v-for="(diffusion, index) in filteredDiffusions" :key="index" :value="diffusion.id">
                        {{ diffusion.name }}
                    </option>
                </select>
            </div>
        </div>
        <div v-show="data.ids.length > 0">
            <slot name="trySearch"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            dafault: () => {
                return {
                	ids: []
                }
            }
        }
    },
    created: function () {

        if (!this.data.ids) {
            this.$set(this.data, "ids", []);
        }

        this.getAllDiffusions();
    },
    computed: {
        filteredDiffusions: function () {
            if (this.$root.diffusion) {
                return this.diffusions.filter((d) => d.id != this.$root.diffusion.id);
            }
            return this.diffusions;
        }
    },
    data: function () {
        return {
            diffusions: []
        }
    },
    methods: {
        getAllDiffusions: async function () {
            let result = await this.ws.get("items", "getAllDiffusions");
            if (!result.error) {
                this.diffusions = result;
            }
        }
    }
}
</script>