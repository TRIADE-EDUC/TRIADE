<template>
    <div>
        <div class="dsi-form-group">
            <label class="etiquette" for="status">{{ messages.get("dsi", "dsi_tag") }}</label>
            <div class="dsi-form-group-content">
                <input :id="'input-' + entity" list="" :value="value" 
                    @input="updateInput" 
                    @keydown.down="increaseIndex" 
                    @keydown.up="decreaseIndex"
                    @keydown.enter.prevent="addTag(focusedIndex == -1 || ! filteredTags[focusedIndex] ? '' : filteredTags[focusedIndex].name)"
                    @focus="getTags"
                    @blur="focusedIndex = -1" />
                <datalist :style="datastyle" class="dsi-datalist" :id="'datalist-'+ entity">
                    <option v-if="showAddOption" class="dsi-datalist-option" mousedown.self="addTag()">
                        <span class="dsi-datalist-label">{{ value }}</span>
                        <button type="button" class="dsi-datalist-button dsi-button bouton" @mousedown="addTag()">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </option>
                    <hr v-if="showAddOption && filteredTags.length" class="dsi-datalist-hr" aria-hidden="true">
                    <span v-for="(tag, index) in filteredTags" :key="index" style="display:inline-block; height : 100%; width:100%;">
                        <option
                            :class="`dsi-datalist-option ${index == focusedIndex ? 'dsi-datalist-option-active' : ''}`" 
                            :value="tag.name" 
                            @mousedown.self="addTag(tag.name)">
                            <span @mousedown.self="addTag(tag.name)">{{ tag.name }}</span>
                            <button type="button" class="dsi-datalist-button dsi-button bouton" @mousedown="deleteTag(tag)">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </option>
                    <hr  class="dsi-datalist-hr" aria-hidden="true">
                    </span>
                </datalist>
            </div>
        </div>
        <modal v-if="showModal" @close="showModal = false">
            <template #header><h2>{{messages.get("dsi", "dsi_tag_warning")}}</h2></template>
            <template #body>
                <div v-for="(entityType, index) in relatedEntities" :key="index">
                    <h3 class="dsi-tags-entities-types">{{messages.get("dsi", entityType.label)}}</h3>
                    <ul>
                        <li v-for="(entity, subIndex) in entityType.entities" :key="subIndex" class="text-left">
                            <span>{{ entity }}</span>
                        </li>
                    </ul>
                </div>
            </template>
            <template #footer>
                <div class="dsi-tags-modal-footer">
                    <button type="button" class="dsi-button bouton" @click="showModal = false">Annuler</button>
                    <button type="button" class="dsi-button bouton" @click="deleteTag(tempDeleteTag, true)">{{messages.get("dsi", "dsi_tag_delete_confirm")}}</button>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
import modal from './modal.vue';
export default {
    props : ['value', "entity", "entityId"],
    components : {
        modal
    },
    data : function() {
        return {
            tags : [],
            focusedIndex : -1,
            showModal : false,
            relatedEntities : [],
            tempDeleteTag : {}
        }
    },
    created : function() {
        this.getTags();
    },
    computed : {
        filteredTags : function() {
            if(! this.tags.length && this.value === undefined) {
                return [];
            }
            return this.tags.filter((t) => t.name.toLowerCase().includes(this.value.toLowerCase()));
        },
        showAddOption : function() {
            if(this.value == "") {
                return false;
            }
            let i = this.filteredTags.findIndex((t) => t.name.toLowerCase() == this.value.toLowerCase());
            if(i == -1) {
                return true;
            }
            return false;
        },
        datastyle : function() {
            let show = {display : "block"};
            let hide = {display : "none"};
            if(this.value == "") {
                return hide;
            }
            if(this.filteredTags.length || this.showAddOption) {
                let input = document.getElementById('input-' + this.entity);
                if(input == document.activeElement) {
                    return show;
                }
            }
            return hide;
        }
    },
    methods : {
        deleteTag : async function(tag, forceDelete = false) {
            if(forceDelete) {
                await this.removeTag(tag)
                if(this.showModal) {
                    this.showModal = false;
                }
                return;
            }
            let selected = await this.ws.get("tags", "getRelatedEntities/" + tag.id);
            if(selected.error) {
                this.notif.error(this.messages.get('dsi', link.errorMessage));
                return;
            }
            if(Object.keys(selected).length) {
                this.$set(this, "relatedEntities", selected);
                this.$set(this, "tempDeleteTag", tag);
                this.showModal = true;
            } else {
                if(confirm(this.messages.get("dsi", "dsi_tag_delete_alert"))) {
                    this.removeTag(tag);
                }
            }
        },
        removeTag : async function(tag) {
            let result = await this.ws.post("tags", "delete", tag);
            if(! result.error) {
                let i = this.tags.findIndex((t) => t.name == tag.name);
                if(i != -1) {
                    this.$delete(this.tags, i);
                }
                this.$emit('input', '');
                this.$emit("removeTag", tag);
            }
               
        },
        setTag : async function(tag) {
            let link = await this.ws.post(this.entity, "linkTag", {
                numTag : tag.id,
                numEntity : this.entityId
            });
            if(! link.error) {
                this.$emit("addTag", tag);
                this.$set(this.tags, this.tags.length, tag);
            } else {
                this.notif.error(this.messages.get('dsi', link.errorMessage));
            }
        },
        addTag : async function(tagName = "") {
            let i = this.filteredTags.findIndex((t) => {
                if(tag != "") {
                    return t.name.toLowerCase() == tagName.toLowerCase()
                } else {
                    return t.name.toLowerCase() == this.value.toLowerCase()
                }
            });
            if(i != -1) {
                await this.setTag(this.filteredTags[i]);
                return;
            }

            let tag = await this.ws.post("tags", "save", { name : this.value });
            if(tag.error) {
                this.notif.error(this.messages.get('dsi', tag.errorMessage));
            } else {
                await this.setTag(tag);
                this.$emit('input', '');
            }
        },
        increaseIndex : function() {
            //Si l'element focus est un multiple du nombre d'elements, on scroll
            if(Number.isInteger(parseInt(this.focusedIndex + 1) / this.Const.tags.nbTagsDatalist) && this.focusedIndex != 1) {
                let datalist = document.getElementById('datalist-' + this.entity);
                datalist.scroll(0, 165);
            } 

            if(this.focusedIndex + 1 < this.filteredTags.length) {
                this.focusedIndex ++;
            }
        },
        decreaseIndex : function() {
            if(Number.isInteger(parseInt(this.focusedIndex) / this.Const.tags.nbTagsDatalist) && this.focusedIndex != 1) {
                let datalist = document.getElementById('datalist-' + this.entity);
                datalist.scrollBy(0, -165);
            }

            if(this.focusedIndex - 1 >= 0) {
                this.focusedIndex --;
            }
        },
        updateInput : function(e) {
            this.$emit('input', e.target.value);
        },
        getTags : async function() {
            if(!this.$root.tags) {
                this.tags = await this.ws.get("tags", "tags");
                return;
            }

            this.tags = JSON.parse(JSON.stringify(this.$root.tags));
        }
    }
}
</script>