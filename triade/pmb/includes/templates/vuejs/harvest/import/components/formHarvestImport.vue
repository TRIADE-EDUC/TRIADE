<template>
    <form id="form-harvest-import" @submit.prevent="save">
        <label for="harvest-profil-import-name">{{ messages.get("harvest", "harvest_profil_import_name") }}</label>
        <input id="harvest-profil-import-name" type="text" v-model="profil.name" required />
        <form-harvest-import-field v-for="(group, i) in orderedGroups"
            :key="i"
            :group="profil.groups[group['ufield']]"
            :flags="getAllowedFlags(group)" />
        <button class="bouton" @click.prevent="cancel">{{ messages.get('common', 'cancel') }}</button>
        <button class="bouton" type="submit">{{ messages.get('common', 'submit') }}</button>
        <button class="bouton right" @click.prevent="deleteImportProfil" v-if="profil.id">{{ messages.get('common', 'remove') }}</button>
    </form>
</template>

<script>
import formHarvestImportField from './formHarvestImportField.vue'
export default {
    name : "formHarvestImport",
    props : ["profil", "flags", "url"],
    components : { formHarvestImportField },
    computed: {
        orderedGroups : function() {
            return Object.values(this.profil.groups).sort((a, b) => a.id - b.id);
        }
    },
    methods : {
        cancel : function() {
            document.location = this.url;
        },
        getAllowedFlags : function(group) {
            const noRepeatable = group.repeatable === "no";
            if (noRepeatable)  {
                // Si on est pas répétable, on doit retirer les flags autorisés pour les champs repétables
                return this.flags.filter(f => !f.onlyRepeatable);
            }
            return this.flags;
        },
        checkForm : function() {
            for (const ufield in this.profil.groups) {
                const group = this.profil.groups[ufield];
                if (this.getAllowedFlags(group).find(f => f.value == group.flag)) {
                    return true;
                }
                return false;
            }
        },
        save : async function() {
            if (!this.checkForm()) {
                console.error("[ERROR] Form not valid");
                return false;
            }

            let result = await this.ws.post("harvest", "profil/import/save", this.profil);
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
        deleteImportProfil : async function() {
            if (confirm(this.messages.get("harvest", "confirm_profil_import_delete"))) {
                let response = await this.ws.post("harvest", "profil/import/delete", this.profil);
                if(! response.error) {
                    document.location = this.url;
                }
            }
        }
    }
}
</script>