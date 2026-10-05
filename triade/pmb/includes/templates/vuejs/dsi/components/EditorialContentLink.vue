<template>
    <div>
        <div class="dsi-form-group">
            <label class="etiquette" for="cms_page">
                {{ messages.get('dsi', 'cms_page') }}
            </label>
            <div class="dsi-form-group-content">
                <select id="cms_page" name="cms_page" class="saisie-50em" v-model="values.pageId">
                    <option v-for="(page, index) in cmsPages" :value="page.value" :key="index">
                        {{ page.label }}
                    </option>
                </select>
            </div>
        </div>
        <div class="dsi-form-group">
            <label class="etiquette" for="cms_page_var">
                {{ messages.get('dsi', 'cms_page_var') }}
            </label>
            <div class="dsi-form-group-content">
                <select id="cms_page_var" name="cms_page_var" class="saisie-50em" v-model="values.varId">
                    <option v-for="(variable, index) in vars" :value="variable.value" :key="index">
                        {{ variable.label }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props : ['pageId', 'varId'],
    data: function () {
        return {
            cmsPages: [],
            values: {
                "pageId": 0,
                "varId": 0,
            }
        }
    },
    created: function () {
        this.values.pageId = this.pageId;
        this.values.varId = this.varId;
        this.fetchCmsPages();
    },
    watch: {
        "values.pageId": function (newValue, oldValue) {
            this.dispatchUpdate();
        },
        "values.varId": function (newValue, oldValue) {
            this.checkVars();
            this.dispatchUpdate();
        }
    },
    computed: {
        vars: function () {
            const page = this.cmsPages.find(page => page.value == this.values.pageId);
            if (!page) {
                return [];
            }
            return page.vars || [];
        }
    },
    methods: {
        fetchCmsPages: async function () {
            let response = await this.ws.get("cms", "pages");
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "cmsPages", response);
            }
        },
        checkVars: function () {

            if (this.values.valueId === 0) {
                return false;
            }

            const variable = this.vars.find(variable => variable.value == this.values.valueId);
            if (typeof variable != "undefined" && variable) {
                this.values.valueId = 0;
            }
        },
        dispatchUpdate: function () {
            this.$emit("update", this.values);
        }
    }
}
</script>