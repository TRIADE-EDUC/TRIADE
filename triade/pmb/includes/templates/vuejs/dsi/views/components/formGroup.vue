<template>
    <div v-if="view && groups.length">
        <div class="dsi-form-group">
            <label class="etiquette dsi-filters-labels">{{ messages.get('dsi', 'view_form_group') }}</label>
            <div class="dsi-form-group-content">
                <select v-model="selectedGroup" class="dsi-select">
                    <option disabled value="">{{ messages.get('dsi', 'view_form_group_default_value') }}</option>
                    <option v-for="(group, index) in groups" :value="index" :key="index">{{ group.name }}</option>
                </select>
                <button class="bouton" type="button" @click="addGroup" :disabled="emptySelectedGroup">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <hr>
        </div>
        <div v-for="(group, index) in view.settings.groups" :key="index">
            <div class="dsi-group">
                <component class="dsi-group-content" :is="group.component" :group="group" @update="updateGroup(index, $event)" />
                <button class="bouton" type="button" @click="removeGroup(index)" style="height: 30px;">
                    <i class="fa fa-remove" aria-hidden="true"></i>
                </button>
            </div>
            <div v-if="index < view.settings.groups.length -1" class="row">
                <hr>
            </div>
        </div>


    </div>
</template>

<script>
import RecordCustomFields from "./groups/RecordCustomFields.vue";
import RecordFacets from "./groups/RecordFacets.vue";

export default {
    name : "formGroup",
    props : ['item', "view"],
    data: function () {
        return {
            groups : [],
            selectedGroup : ""
        }
    },
    watch: {
        "item.type" : async function() {
            await this.getGroups();
        },
        "view.type" : async function(newValue, oldValue) {
            if (newValue != oldValue) {
                await this.getGroups();
                if (!this.groups?.length && this.view.settings.groups) {
                    this.$delete(this.view.settings, "groups");
                }
            }
        }
    },
    components: {
        RecordCustomFields,
        RecordFacets
    },
    created: function() {
        if (this.view) {
            if (!this.view.settings.groups) {
                this.$set(this.view.settings, "groups", []);
            }
            this.getGroups();
        }
    },
    computed: {
        emptySelectedGroup: function () {
            return this.selectedGroup === "";
        }
    },
    methods: {
        getGroups : async function() {
            if(this.view && this.view.type) {
                let response = await this.ws.get("group", `compatibility/${this.view.type}/${this.item.type}`);
                if (! response.error) {
                    this.groups = response;
                }
            }
        },
        addGroup: function () {
            if (this.emptySelectedGroup) {
                return false;
            }

            this.view.settings.groups.push({
                ...this.groups[this.selectedGroup]
            });
            this.selectedGroup = "";
        },
        removeGroup: function (index) {
            this.$delete(this.view.settings.groups, index);
        },
        updateGroup: function (index, group) {
            this.$set(this.view.settings.groups, index, group);
        }
    }
}
</script>