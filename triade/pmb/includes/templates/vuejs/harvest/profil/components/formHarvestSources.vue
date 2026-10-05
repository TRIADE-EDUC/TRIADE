<template>
    <fieldset class="harvest-profil-sources">
        <legend>{{ messages.get("harvest", "source_field_selector_legend") }}</legend>
        <div v-for="source in sources" :key="source.id" class="harvest-profil-source">
            <label :for="'source-select-' + source.id">{{source.name}}</label>
            <select :id="'source-select-' + source.id" v-model="source.field">
                <option value="0">{{ messages.get('harvest', 'source_field_selector_label') }}</option>
                <option v-for="field in source.searchFields" :key="field.value" :value="field.value">{{ field.label }}</option>
            </select>
        </div>
    </fieldset>
</template>

<script>
export default {
    name : "formHarvestSources",
    props : ["sources"]
}
</script>