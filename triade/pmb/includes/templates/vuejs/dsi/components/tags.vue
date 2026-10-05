<template>
    <div>
        <tag v-model="tagInput" :entity="entity" :entity-id="entityId" @addTag="addTag($event)" @removeTag="removeTag($event)"></tag>
        <div v-if="tags && tags.length" class="dsi-cards">
            <div class="dsi-card dsi-tag-card" v-for="(tag, index) in tags" :key="index">
                <p>{{ tag.name }}</p>
                <button type="button" class="dsi-button bouton" @click="removeTagFromEntity(index)">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import tag from './tag.vue';
export default {
    components: { 
        tag 
    },
    props : ['tags', 'entity', 'entityId'],
    data : function() {
        return {
            tagInput : ""
        }
    },
    created : function() {
        this.$root.$on("importModelTags", (e) => {
            if(e.entityType == this.entity && e.entityId == this.entityId) {
                this.importModelTags();
            }
        });
    },
    methods : {
        removeTagFromEntity : async function(i) {
            let deleted = await this.ws.post(this.entity, "unlinkTag", { 
                numTag : this.tags[i].id,
                numEntity : this.entityId
            })
            if(! deleted.error) {
                this.$delete(this.tags, i);
            }
        },
        addTag : function(tag) {
            this.$set(this.tags, this.tags.length, tag);
            this.$set(this, "tagInput", "");
        },
        removeTag : function(tag) {
            let i = this.tags.findIndex(t => t.name == tag.name);
            if(i != -1) {
                this.$delete(this.tags, i);
            }
        },
        importModelTags : async function() {
            if(confirm(this.messages.get('dsi', 'tags_import_model_tags_confirm'))) {
                let response = await this.ws.post(this.entity, "importModelTags", {
                    numEntity : this.entityId
                });
    
                if(! response.error) {
                    this.$emit("newTagList", response);
                }
            }
        }
    }
}
</script>