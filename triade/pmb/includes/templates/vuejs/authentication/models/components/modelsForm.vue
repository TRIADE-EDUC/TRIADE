<template>
    <div>
        <template v-for="(sourceItem, index) in manifests_list" >
            <accordion :title="sourceItem.name" :index="index" :key="index" :expanded="true">
                <table class="sources-table">
                    <thead>
                        <tr>
                            <th>
                            {{ messages.get('authentication', 'authentication_add_model') }}
                            <button class="bouton right" @click="addModel(sourceItem.name)">
                                <i class="fa fa-plus"></i>
                            </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(modelItem, modelIndex) in modelsList" :key="modelIndex"
                            :class="['source-item cursor-pointer', index%2 ? 'odd' : 'even', hover == modelIndex ? 'surbrillance' : '']"
                            @mouseover="hover = index" @mouseout="hover = null">
                            <td v-if="index == modelItem.source_name" class="source-item-name">
                                {{ modelItem.name }}
                                <button class="bouton right" @click="updateModel(modelItem.id)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </accordion>
        </template> 
    </div>
</template>

<script>

    import accordion from "../../../common/accordion/accordion.vue";

	export default {
		props : ["action", "manifests_list", "models_list"],
		data: function () {
			return {
			    hover: null,
			    modelsList: []
			}
		},

		components : {
			accordion
		},

		created: function() {
			this.modelsList = this.helper.cloneObject(this.models_list);
		    this.$root.$on('updateModelList', this.updateModelList);
		},

 		computed: {
		},

		methods: {
			updateModel: function( modelId) {
		        this.$emit('updateModel', {
		            'modelId': modelId
		        });
		    },

		    addModel: function(sourceName) {
		        this.$emit('addModel', {
		            'sourceName': sourceName,
		        });
		    },

		    updateModelList: async function () {
		    	let response = await this.ws.get(this.index, "getModelsList");
		    	this.modelsList = response.models_list;
		    }
		}
	}
</script>