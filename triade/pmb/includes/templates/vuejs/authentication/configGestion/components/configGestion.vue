<template>
    <div class="grid-2-col">
		<div id="modelsList">
		    <configForm 
		          :action="action"
	              :configs_list="configs_list"
	              :models_list="models_list"
	              :allow_internal_gestion_authentication="allow_internal_gestion_authentication"
	              @getConfigByModelForm="getConfigByModelForm($event)" @updateConfig="getConfigForm($event)"></configForm>
		</div>
	    <div v-if="showComponent" id="sourceDetails">
	        <commonForm @cancel="cancel" 
	           :empr_search_class_list="empr_search_class_list" 
	           :empr_create_class_list="empr_create_class_list" 
	           :user_search_class_list="user_search_class_list" 
               :user_create_class_list="user_create_class_list"
	           :transfo_class_list="transfo_class_list" 
	           :data="data"
               context="2"
           ></commonForm>
	        <div v-if="!showComponent && sourceName">
	            <img :src="images.get('patience.gif')" :alt="messages.get('common', 'wait')" :title="messages.get('common', 'wait')">
	        </div>
	    </div>
    </div>
</template>

<script>
    import configForm from "./configForm.vue";
    import commonForm from "../../common/commonConfig/commonForm.vue";

    export default {
        props : [
        	"action", 
        	"manifests_list", 
        	"models_list", 
        	"configs_list",
        	"empr_search_class_list", 
            "empr_create_class_list", 
        	"user_search_class_list", 
            "user_create_class_list", 
        	"transfo_class_list", 
        	"allow_internal_gestion_authentication",
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
        	configForm,
        	commonForm
        },

        created: function() {
            this.$root.$on('deleteConfigEvent', this.deleteConfigEvent);
        },

        methods: {
            cancel: function(event) {
            	this.showComponent = false;
            	this.sourceName = null;
            },

            getConfigByModelForm: async function(event) {
            	this.showComponent = false;
                this.data = await this.ws.get("getConfigByModelForm", event);
                this.$set(this.data.model, "id", 0);
                this.data.sourceName = this.data.manifest.name;
                this.showComponent = true;
            },

            getConfigForm: async function(event) {
            	this.showComponent = false;
                this.data = await this.ws.get("getConfigForm", event.configlId);
                this.data.sourceName = this.data.manifest.name;
                this.showComponent = true;
            },

            deleteConfigEvent: function(event) {
                this.showComponent = false;
                this.sourceName = null;
            }
        }
    }
</script>