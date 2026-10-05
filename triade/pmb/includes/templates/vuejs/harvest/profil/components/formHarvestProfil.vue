<template>
    <form id="form-harvest-profil" name="form-harvest-profil" @submit.prevent="save">
        <label for="harvest-profil-name">{{ messages.get("harvest", "harvest_profil_name") }}</label>
        <input id="harvest-profil-name" type="text" v-model="profil.name" required />
        <form-harvest-sources :sources="profil.sources"></form-harvest-sources>
        <hr>
        <form-harvest-fields-group v-for="(group, i) in orderedGroups"
            :key="i"
            :sources="profil.sources"
            :group="profil.groups[group['ufield']]"
            @addfield="addField"
            @removefield="removeField">
        </form-harvest-fields-group>
        <button class="bouton" @click.prevent="cancel">{{ messages.get('common', 'cancel') }}</button>
        <button class="bouton" type="submit">{{ messages.get('common', 'submit') }}</button>
        <button class="bouton right" @click.prevent="deleteProfil" v-if="profil.id">{{ messages.get('common', 'remove') }}</button>
    </form>
</template>

<script>
import FormHarvestFieldsGroup from './formHarvestFieldsGroup.vue';
import formHarvestSources from './formHarvestSources.vue';

export default {
    name : "formHarvestProfil",
    props : ["profil", "url"],
    components: {
        formHarvestSources,
        FormHarvestFieldsGroup
    },
    computed: {
        orderedGroups : function() {
            return Object.values(this.profil.groups).sort((a, b) => a.id - b.id);
        }
    },
    methods : {
        getGroupFields : function(id) {
            return this.profil.fields.filter((f) => f.xmlId == id);
        },
        cancel : function() {
            document.location = this.url
        },
        save : async function() {
            let result = await this.ws.post("harvest", "profil/save", this.profil);
            if (! result.error) {
                if(this.profil.id) {
                    this.notif.info(this.messages.get("common", "success_save"));
                } else {
                    document.location = this.url + "&action=modif&id=" + result.id;
                }
            } else {
                this.notif.error(this.messages.get("common", "failed_save"));
            }
        },
        addField : function(data) {
            let field = data.field;
            let i = data.groupField;
            //On cherche le groupe de champ par son id
            if(this.profil.groups[i]) {
                this.profil.groups[i].fields.push(field);
            }
        },
        removeField : function(data) {
            let field = data.field;
            let i = data.groupField;
            if(this.profil.groups[i]) {
                let j = this.profil.groups[i].fields.findIndex(f => f.order == field.order);
                if(j != -1) {
                    let found = true;
                    let order = parseInt(this.profil.groups[i].fields[j].order) + 1;
                    //Suppression du champ
                    this.profil.groups[i].fields.splice(j, 1);
                    //Mise a jour de l'ordre des autres champs
                    do {
                        let k = this.profil.groups[i].fields.findIndex(f => f.order == order);
                        if(k != -1) {
                            this.profil.groups[i].fields[k].order --;
                            order++;
                            continue;
                        }
                        found = false;
                    } while(found);
                }
            }
        },
        deleteProfil : async function() {
            if(confirm(this.messages.get("harvest", "confirm_profil_delete"))) {
                let response = await this.ws.post("harvest", "profil/delete", this.profil);
                if(! response.error) {
                    document.location = this.url;
                }
            }
        }
    }
}
</script>