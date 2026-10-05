<template>
	<div>
		<form class="form-admin" action="" method="POST" @submit.prevent="add">
			<div class="form-contenu">
				<div class="row group-form">
					<label class="etiquette" for="lvl_bibli">{{ pivot.messages.nivbiblio }}</label>
					<select id="lvl_bibli" name="lvl_bibli" v-model="selected.nivbiblio" required>
						<option v-for="(label, code) in pivot.nivbiblio" :value="code" :key="code">
							{{ label }}
						</option>
					</select>
				</div>
			
				<div class="row group-form">
					<label class="etiquette" for="doc_type">{{ pivot.messages.doctype }}</label>
					<div class="group-form-action">
						<select id="doc_type" name="doc_type" v-model="selected.typedoc">
							<option v-for="(label, code) in typedoclist" :value="code" :key="code">
								{{ label }}
							</option>
						</select>
						<button class="bouton" type="submit">
							{{ messages.get("common", "more") }}
						</button>
					</div>
				</div>
			</div>
		</form>
			
		<table class="sources-table">
			<thead>
				<tr>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in list" :key="index"
					:class="['source-item', index%2 ? 'odd' : 'even', hover == index ? 'surbrillance' : '']"
					@mouseover="hover = index" @mouseout="hover = null">
					<td @click="clicked(item, index)" class="source-item-name cursor-pointer">
						{{ item.label }}
					</td>
					<td class="source-item-name">
						<button v-if="item.pivot.nivbiblio" 
							class="bouton" type="button" @click="remove(index)" 
							:title="messages.get('common', 'remove')">
							{{ messages.get("common", "remove_short") }}
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		props : ["pivot"],
		data: function () {
			return {
			    hover: null,
			    pivots: [],
			    selected: {
			        typedoc: "",
			        nivbiblio: ""
			    },
			    selectedIndex : 0,
			}
		},
		created: function() {
			this.pivots = this.helper.cloneObject(this.pivot.pivots);
		},
		computed: {
		    typedoclist: function() {
				const matches = this.pivots.filter(item => item.pivot.nivbiblio == this.selected.nivbiblio);
				const typedocUsed = matches.map(item => item.pivot.typedoc);
				if (!typedocUsed.length) {				    
					return this.pivot.typedoc;
				}
				const list = Object.entries(this.pivot.typedoc).filter(item => !typedocUsed.includes(item[0]));
				return Object.fromEntries(list);
		    },
		    list: function() {
		        let list = new Array();
		        for (let item of this.pivots) {
		            list.push({
		                ...item,
		                label: this.makeLabel(item.pivot.typedoc, item.pivot.nivbiblio)
		            });
		        }
		        return list;
		    }
		},
		methods: {
		    add: function() {
		        this.pivots.push({
		            pivot: this.helper.cloneObject(this.selected),
			        sources: []
		        });
		        
		        this.selected.typedoc = "";
		        this.selected.nivbiblio = "";
		    },
		    remove: async function(index) {
				if (confirm(this.messages.get("thumbnail", "remove_pivot_confirm"))) {
					const pivotData = {
			                ...this.pivots[index].pivot,
			                namespace: this.pivot.namespace,
					}
					let response = await this.ws.post("pivot", "record/remove", {
						pivot : pivotData,
					});
					if (response.error) {
	                    if (response.errorMessage) {
	                        console.error(response.errorMessage);
	                    }
	                    this.notif.error(this.messages.get("common", "failed_operation"));
	                } else {
		        		this.pivots.splice(index, 1);
	                    this.notif.info(this.messages.get("common", "successful_operation"));
	                    if (index == this.selectedIndex) {
	                    	this.$emit("cleanForm");
	                    }
	                }
				}
		    },
		    clicked: function(item, index) {
		        this.$emit("selected", item);
		        this.selectedIndex = index;
		    },
		    makeLabel: function (typedoc, nivbiblio) {
		        if (!typedoc && !nivbiblio) {
		            return this.pivot.messages.default;
		        }
		        if (!typedoc) {
		        	return this.pivot.nivbiblio[nivbiblio];
		        }
		        return this.pivot.nivbiblio[nivbiblio] + " / " + this.pivot.typedoc[typedoc];
		    }
		}
	}
</script>