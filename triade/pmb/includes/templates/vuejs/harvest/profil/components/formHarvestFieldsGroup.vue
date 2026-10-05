<template>
    <fieldset class="harvest-profil-field">
        <legend>{{group.name}} ({{group.ufield}})</legend>
        <label :for="'flag-first-' + group.id">{{ messages.get("harvest", "harvest_profil_field_flag_first") }}</label>
        <input :id="'flag-first-' + group.id" type="checkbox" v-model="group.firstFlag" :true-value="1" :false-value="0" />
        <form-harvest-field v-for="(field, i) in group.fields"
            :key="i"
            :field="field"
            :sources="getfilteredSources(field.source)"
            :group="group"
            :disable-add-button="disableAddButton"
            @addfield="addField"
            @removefield="removeField"></form-harvest-field>
    </fieldset>
</template>

<script>
import formHarvestField from './formHarvestField.vue'

export default {
    name : "formHarvestFieldsGroup",
    props : ["sources", "group"],
    components : { formHarvestField },
    methods : {
        addField : function() {
            this.$emit("addfield",
            {
                groupField : this.group.ufield,
                field : {
                    source : 0,
                    ufield : this.group.ufield,
                    precFlag : 0,
                    order : this.group.fields.length
                }
            });
        },
        removeField : function(i) {
            this.$emit("removefield", { groupField : this.group.ufield, field : this.group.fields[i] });
        },
        getfilteredSources : function(sourceId) {
            return this.sources.filter(s => this.group.fields.findIndex(f => f.source == s.id && s.id != sourceId) == -1);
        }
    },
    computed : {
        disableAddButton : function() {
            if(! this.group.fields || ! this.group.fields.length) {
                return false;
            }
            for(let field of this.group.fields) {
                if(field.source == 0 || field.ufield == '') {
                    return true;
                }
            }
            return false;
        }
    }
}
</script>