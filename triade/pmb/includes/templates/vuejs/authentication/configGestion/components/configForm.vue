<template>
    <div>
      <table class="sources-table">
          <thead>
              <tr>
                  <th colspan="2">
                        {{ messages.get('authentication', 'authentication_add_config') }}
                  </th>
              </tr>
              <tr>
                  <th colspan="2">
                      <select v-model="selectedModel">
                           <option value="0">Choisir</option>
                           <option v-for="(model, modelIndex) in models_list" :key="modelIndex" :value="model.id">{{ model.name }}</option>
                      </select>
                      <button class="bouton right" @click="getConfigByModelForm()">
                          <i class="fa fa-plus"></i>
                      </button>
                  </th>
              </tr>
          </thead>
          <tbody>
              <tr class="source-item-name">
                  <td class="source-item-name">
                       Connexion Interne
                  </td>
                  <td class="source-item-name">
                       <input 
                            type="checkbox" 
                            class="switch" 
                            name="automatic-active" 
                            id="automatic-active" 
                            v_model="allowInternalGestionAuthentication"
                            @click="updateAllowInternalGestion"
                            :checked="(allowInternalGestionAuthentication ? 'checked' : '')"
                            />
                       <label for="automatic-active">&nbsp;</label>
                  </td>
              </tr>
              <tr v-for="(configItem, configIndex) in configsList" :key="configIndex"
                  :class="['source-item cursor-pointer', configIndex%2 ? 'odd' : 'even', hover == configIndex ? 'surbrillance' : '']"
                  @mouseover="hover = configIndex" @mouseout="hover = null">
                  <td class="source-item-name">
                      {{ configItem.name }}
                  </td>
                  <td class="link-item-action">
                       <button class="bouton" type="button" @click="moveConfig(configIndex, 'up')" :disabled="upDisabled(configIndex)">
                            <i class="fa fa-arrow-up" :alt="messages.get('common', 'up')"></i>
                       </button>
                       <button class="bouton" type="button" @click="moveConfig(configIndex, 'down')" :disabled="downDisabled(configIndex)">
                            <i class="fa fa-arrow-down" :alt="messages.get('common', 'down')"></i>
                       </button>
                       <button class="bouton right" @click="updateConfig(configItem.id)">
                         <i class="fa fa-pencil"></i>
                       </button>
                   </td>
              </tr>
          </tbody>
      </table>
    </div>
</template>

<script>

	export default {
		props : ["action", "configs_list", "models_list", "allow_internal_gestion_authentication"],
		data: function () {
			return {
			    hover: null,
			    configsList: [],
			    selectedModel: 0,
			    allowInternalGestionAuthentication: 0
			}
		},

		components : {
		},

		created: function() {
			this.configsList = this.helper.cloneObject(this.configs_list);
		    this.$root.$on('updateConfigsList', this.updateConfigsList);
		    this.allowInternalGestionAuthentication = this.allow_internal_gestion_authentication;
		},

 		computed: {
		},

		methods: {
			updateConfig: function( configlId) {
		        this.$emit('updateConfig', {
		            'configlId': configlId
		        });
		    },

		    getConfigByModelForm: function() {
		    	if(this.selectedModel) {
			        this.$emit('getConfigByModelForm', this.selectedModel);
		    	}
		    },

		    updateConfigsList: async function () {
		    	let event = {
	    		    "action": "getConfigsList",
	    		    "context": "gestion"
	    		};

		    	const values = Object.values(event); // Result : ['getConfigsList', 'gestion']
		    	let url = values.join('/'); // Result : getConfigsList/gestion

		    	let response = await this.ws.get(this.configIndex, url); // url : ./rest.php/source/getConfigsList/opac
		    	//let response = await this.ws.get(this.configIndex, "getConfigsList");
		    	this.configsList = response.configs_list;
		    },

            downDisabled: function(index) {
                return index == this.configsList.length - 1;
            },

            upDisabled: function(index) {
                return index == 0;
            },

            moveConfig: async function(index, sens) {
                let newIndex;
                const item = this.configsList[index];
                this.configsList.splice(index, 1);
                if("down" == sens) {
                    newIndex = index + 1;
                } else {
                    newIndex = index - 1;
                }
                this.configsList.splice(newIndex, 0, item);

                let response = await this.ws.post("configuration", "moveConfig", {
                    configsList: this.configsList,
                });

                this.$root.$emit('updateConfigsList');
            },

            updateAllowInternalGestion: async function() {
                let response = await this.ws.get("updateAllowInternalGestion", this.allowInternalGestionAuthentication);
                this.$set(this, "allowInternalGestionAuthentication", response.state);
            }
		}
	}
</script>