<template>
	<div>
		<h4 v-if="formData && formData.label">
			{{ formData.label }}
		</h4>
		
		<form class="form-admin thumbnail-small-form" action="" method="POST" @submit.prevent="add">
			<div class="form-contenu">
				<div class="row group-form">
					<label for="source" class="etiquette">{{ messages.get("thumbnail", "thumbnail_sources") }}</label>
					<div class="group-form-action">
						<select id="source" class="select-sources" name="source" required v-model="sourceSelected">
							<option :value="index" v-for="(source, index) in sourcesList" :key="index">
								{{ source.label }}
							</option>
						</select>
						<button class="bouton" type="submit">
							{{ messages.get("common", "more") }}
						</button>
					</div>
				</div>
			</div>
		</form>
		
		
		<table class="sources-table" v-if="list && list.length">
			<thead>
				<tr>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(sourceNamespace, index) in list" :key="index"
					:class="['source-item', index%2 ? 'odd' : 'even', hover == index ? 'surbrillance' : '']"
					@mouseover="hover = index" @mouseout="hover = null">
					<td class="source-item-name">
						{{ getSourcelabel(sourceNamespace) }}
					</td>
					<td class="source-item-action">
						<button :class="['bouton up', upDisabled(index) ? 'disabled' : '']" type="button" @click="down(index)" :disabled="upDisabled(index)">
							<img :src="images.get('top-arrow.png')" :alt="messages.get('common', 'up')">
						</button>
						<button :class="['bouton down', downDisabled(index) ? 'disabled' : '']" type="button" @click="up(index)" :disabled="downDisabled(index)">
							<img :src="images.get('bottom-arrow.png')" :alt="messages.get('common', 'down')">
						</button>
						<button class="bouton" type="button" @click="remove(index)" :title="messages.get('common', 'remove')">
							{{ messages.get("common", "remove_short") }}
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="row">
			<button class="bouton btnCancel" type="button" @click="cancel">
				{{ messages.get("common", "cancel") }}
			</button>
			<button class="bouton" type="submit"  @click="submit">
				{{ messages.get("common", "submit") }}
			</button>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["pivot", "formData", "sources"],
		data: function () {
			return {
			    hover: null,
			    sourceSelected: "",
			    list: []
			}
		},
		mounted: function() {
			this.list = this.helper.cloneObject(this.formData.sources ?? []);
		},
		updated: function() {
			if (typeof domUpdated	=== "function") {
			    domUpdated();
			}
		},
		computed: {	
		    sourcesList: function() {
				var list = this.sources.filter(source => {
				    const match = this.list.find(sourceNamespace => sourceNamespace == source.namespace);
					return match ? false : true;
				});
				return list;
		    }
		},
		methods: {
		    getSourcelabel: function(namespace) {
		        const find = this.sources.find(source => source.namespace == namespace);
		        return find ? find.label : "";
		    }, 
		    downDisabled: function(index) {
		        return index == this.list.length - 1;
		    }, 
		    upDisabled: function(index) {
		        return index == 0;
		    }, 
		    cancel: function () {
		        this.$emit('cancel');
		    },
		    submit: async function () {
		        const pivot = {
	                ...this.formData.pivot,
	                namespace: this.pivot.namespace,
		        } 
		        
		        let response = await this.ws.post("pivot", "record/save", {
		            pivot: pivot, 
		            sources: this.list
		        });
				if (response.error) {
				    if (response.errorMessage) {
				        console.error(response.errorMessage);
				    }
					this.notif.error(this.messages.get("common", "failed_save"));
				} else {
					this.notif.info(this.messages.get("common", "success_save"));
				}
		    },
		    add: function () {
		        this.list.push(this.sourcesList[this.sourceSelected].namespace);
		        this.sourceSelected = "";
		    },
		    remove: function (index) {
		        this.list.splice(index, 1);
		    },
		    up: function (index) {
		        this.list.splice(index+2, 0, this.list[index])
		        this.list.splice(index, 1)
		    },
		    down: function (index) {
		        this.list.splice(index-1, 0, this.list[index])
		        this.list.splice(index+1, 1)
		    }
		}
	}
</script>