<template>
    <div class="grid-2-col">
		<div id="modelsList">
		    <modelsForm 
		          :action="action"
	              :manifests_list="manifests_list"
	              :models_list="models_list"
	              @addModel="getForm($event)" @updateModel="getModelForm($event)"></modelsForm>
		</div>
	    <div v-if="showComponent" id="sourceDetails">
	        <commonForm @cancel="cancel" 
	           :empr_search_class_list="empr_search_class_list" 
	           :empr_create_class_list="empr_create_class_list" 
	           :user_search_class_list="user_search_class_list" 
	           :user_create_class_list="user_create_class_list" 
	           :transfo_class_list="transfo_class_list" 
	           :data="data" ></commonForm>
	        <div v-if="!showComponent && sourceName">
	            <img :src="images.get('patience.gif')" :alt="messages.get('common', 'wait')" :title="messages.get('common', 'wait')">
	        </div>
	    </div>
    </div>
</template>

<script>
    import modelsForm from "./modelsForm.vue";
    import commonForm from "./form/commonForm.vue";

    export default {
        props : [
        	"action",
        	"manifests_list",
        	"models_list",
        	"empr_search_class_list",
        	"empr_create_class_list",
        	"user_search_class_list",
        	"user_create_class_list",
        	"transfo_class_list",
        ],
        data: function () {
            return {
                showComponent: false,
                sourceName: null,
                data: {
                }
            }
        },

        components : {
        	modelsForm,
        	commonForm
        },

        created: function() {
            this.$root.$on('deleteModelEvent', this.deleteModelEvent);
        },

        methods: {
            cancel: function(event) {
            	this.showComponent = false;
            	this.sourceName = null;
            },

            getForm: async function(event) {
            	this.showComponent = false;
                this.sourceName = event.sourceName ?? null;
                if (this.sourceName !== null) {
                    this.data = await this.ws.get(event.sourceName, "getForm");
                }
                this.data.sourceName = event.sourceName;
                this.showComponent = true;
            },

            getModelForm: async function(event) {
            	this.showComponent = false;
                this.data = await this.ws.get("getModelForm", event.modelId);
                this.data.sourceName = this.data.manifest.name;
                this.showComponent = true;
            },

            deleteModelEvent: function(event) {
            	this.showComponent = false;
            	this.sourceName = null;
            }
        }
    }
</script>