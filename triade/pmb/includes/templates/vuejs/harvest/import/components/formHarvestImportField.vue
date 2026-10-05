<template>
    <fieldset class="harvest-profil-field">
        <legend>{{ group.name }} ({{ group.ufield }})</legend>
        <span v-for="flag in flags" :key="flag.value">
            <input :id="'flag-select-' + group.id + '-' + flag.value" type="radio" :value="flag.value" v-model="group.flag" />
            <label :for="'flag-select-' + group.id + '-' + flag.value">{{ messages.get("harvest", flag.labelCode) }}</label>
        </span>
    </fieldset>
</template>

<script>
export default {
    name : "formHarvestImportField",
    props : ["flags", "group"],
}
</script>