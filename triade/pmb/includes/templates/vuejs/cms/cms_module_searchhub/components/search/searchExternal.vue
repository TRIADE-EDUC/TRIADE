<template>
    <div class="search-external">
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-form-action">
                    {{ cmsMessages['cms_module_searchhub_profile_search_form_action'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <input type="text" name="action" id="search-settings-form-action" v-model="search.settings.form.action" class="saisie-80em" required>
            </div>
        </div>
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-form-input-name">
                    {{ cmsMessages['cms_module_searchhub_profile_search_form_input_name'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <input type="text" name="input_name" id="search-settings-form-input-name" v-model="search.settings.form.input_name" class="saisie-80em" required>
            </div>
        </div>
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-form-method">
                    {{ cmsMessages['cms_module_searchhub_profile_search_form_method'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <select id="search-settings-form-method" name="method" v-model="search.settings.form.method" class="saisie-80em">
                    <option value="" disabled>{{ cmsMessages['cms_module_searchhub_profile_search_form_method_none'] }}</option>
                    <option value="GET">{{ cmsMessages['cms_module_searchhub_profile_search_form_method_get'] }}</option>
                    <option value="POST">{{ cmsMessages['cms_module_searchhub_profile_search_form_method_post'] }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["search"],
        mounted: function() {
            const formData = this.search.settings.form || { action: '', input_name: '', method: 'POST' };
            this.$set(this.search.settings, 'form', formData);
        }
    }

</script>