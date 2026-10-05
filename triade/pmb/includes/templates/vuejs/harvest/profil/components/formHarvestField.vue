<template>
    <div class="harvest-field">
        <input v-model="field.ufield" required />
        <select v-model="field.source">
            <option value="0" >{{ messages.get("harvest", "source_selector_label") }}</option>
            <option v-for="source in sources" :key="source.id" :value="source.id">{{ source.name }}</option>
        </select>
        <span v-if="field.order > 0">
            <label :for="`prec-flag-checkbox-${group.id}-${field.order}`">{{ messages.get("harvest", "prec_flag_checkbox") }}</label>
            <input :id="`prec-flag-checkbox-${group.id}-${field.order}`" type="checkbox" v-model="field.precFlag" :true-value="1" :false-value="0" />

            <button class="bouton" @click.prevent="$emit('removefield', field.order)">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </span>
        <button class="bouton" @click.prevent="$emit('addfield')" :disabled="disableAddButton" v-if="field.order == 0">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
    </div>
</template>

<script>
export default {
    name : "formHarvestField",
    props : ["field", "sources", "disableAddButton", "group"]
}
</script>