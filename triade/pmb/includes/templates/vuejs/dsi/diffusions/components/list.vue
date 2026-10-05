<template>
	<div id="list">
		<filter-list :list="originalList" :fields="filterFields" @filter="setFilter"></filter-list>
		<pagination-list :list="Object.values(sortedList)" :nbPage="6" :perPage="10" :startPage="1"
		:nbResultDisplay="false">
			<template #content="{ list }">
				<table>
					<thead>
						<tr>
							<th>
								<input type="checkbox" name="batch-action-all" id="batch-action-all" @change="checkAll">
								<i role="button" class="fa fa-th-list" aria-hidden="true" :title="messages.get('dsi', 'dsi_list_check_all')"></i>
							</th>
							<th @click="sortTable('name')" style="cursor: pointer; width: 25%">
								{{ messages.get('dsi', 'diffusion_name') }}
								<i :class="sortColumn === 'name' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th @click="sortTable('automatic')" style="cursor: pointer;">
								{{ messages.get('dsi', 'diffusion_automatic') }}
								<i :class="sortColumn === 'automatic' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th @click="sortTable('channel')" style="cursor: pointer;">
								{{ messages.get('dsi', 'diffusion_channel') }}
								<i :class="sortColumn === 'channel' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th @click="sortTable('nbSubscribers')" style="cursor: pointer;">
								{{ messages.get('dsi', 'diffusion_total_subscribers') }}
								<i :class="sortColumn === 'nbSubscribers' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th @click="sortTable('lastDiffusion')" style="cursor: pointer;">
								{{ messages.get('dsi', 'diffusion_last') }}
								<i :class="sortColumn === 'lastDiffusion' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th @click="sortTable('status')" style="cursor: pointer;">
								{{ messages.get('dsi', 'diffusion_status') }}
								<i :class="sortColumn === 'status' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th>{{ messages.get('dsi', 'dsi_tag_list') }}</th>
							<th class="dsi-table-right"><span>{{ messages.get('dsi', 'actions') }}</span></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(diffusion, index) in list" :key="index">
							<td>
								<input type="checkbox"
									:name="'batch-action-' + index"
									:id="'batch-action-' + index"
									:value="diffusion.id"
									v-model="batchActionList">
							</td>
							<td style="cursor: pointer" @click.stop.prevent="edit(diffusion.id, $event)">
								<a href="#" @click.stop.prevent="edit(diffusion.id, $event)">{{ diffusion.name }}</a>
							</td>
							<td>{{ diffusion.automatic ? messages.get('dsi', 'diffusion_automatic_yes') : messages.get('dsi', 'diffusion_automatic_no') }}</td>
							<td>{{ diffusion.channel }}</td>
							<td>{{ diffusion.nbSubscribers }}</td>
							<td>{{ diffusion.lastDiffusion }}</td>
							<td>{{ diffusion.status }}</td>
							<td>
								<span v-for="(tag, i) in diffusion.tags" :key="i">
									{{ tag.name }}
									<span v-if="(i + 1) < diffusion.tags.length">, </span>
								</span>
							</td>
							<td class="dsi-table-right">
								<button type="button" class="bouton" @click="edit(diffusion.id, $event)">{{ messages.get('dsi', 'edite') }}</button>
								<button type="button" class="bouton" @click="duplicate(diffusion.id)">{{ messages.get('dsi', 'dsi_duplicate') }}</button>
								<button type="button" class="bouton btnDelete" @click="deleteDiffusion(diffusion)">{{ messages.get('dsi', 'del') }}</button>
							</td>
						</tr>
					</tbody>
				</table>
			</template>
		</pagination-list>
		<div class="dsi-batch-all-action">
			<span>{{ messages.get('dsi', 'dsi_list_check_all_actions') }}</span>
			<i role="button" :class="batchActionList.length === 0 ? 'fa fa-trash disabled' : 'fa fa-trash'" aria-hidden="true"
				:title="messages.get('dsi', 'dsi_list_check_all_action_delete')"
				@click="execAction('deleteAll')">
			</i>
		</div>
		<input type="button" class="bouton" :value="messages.get('dsi', 'add')" @click="add">
	</div>
</template>

<script>
	import filterList from "../../components/filterList.vue";
	import paginationList from "../../components/paginationList.vue";
	export default {
		props : ["list", "status", "channels"],
		components: {
			paginationList,
			filterList
		},
		data: function () {
			return {
				sortColumn: 'name',
      			sortDirection: 'asc',
				filterFields: [
					{
						name : "name",
						label : "diffusion_name",
						type : "text"
					},
					{
						name : "automatic",
						label : "diffusion_automatic",
						type : "number"
					},
					{
						name : "channel",
						label : "diffusion_channel",
						type : "text"
					},
					{
						name : "nbSubscribers",
						label : "diffusion_total_subscribers",
						type : "number"
					},
					{
						name : "lastDiffusion",
						label : "diffusion_last",
						type : "text"
					},
					{
						name : "status",
						label : "diffusion_status",
						type : "text"
					},
					{
						name : "tags",
						label : "filter_tags",
						type : "tags"
					}
				],
				filterList: [],
				originalList: [],
				batchActionList: []
			}
		},
		created: function() {
			for (let i=0; i<this.list.length; i++) {
				this.filterList[i] = this.formatListElement(this.list[i]);
			}

			this.originalList = this.filterList;
		},
		computed: {
			sortedList() {
				return this.filterList.sort((a, b) => {
					const column = this.sortColumn;
					const direction = this.sortDirection === 'asc' ? 1 : -1;

					if (a[column] < b[column]) return -1 * direction;
					if (a[column] > b[column]) return 1 * direction;

					return 0;
				});
			},
			carretClass() {
				return 'fa fa-sort-' + this.sortDirection;
			}
		},
		methods: {
			formatListElement : function(element) {
				return {
					"id": element.id,
					"name": element.name,
					"automatic": element.automatic,
					"channel": this.getDiffusionChannel(element.channel.type),
					"status": this.getDiffusionStatus(element.numStatus),
					"lastDiffusion": element.lastDiffusion,
					"nbSubscribers": element.nbSubscribers,
					"tags": element.tags
				}
			},
		    add: function () {
		        document.location = "./dsi.php?categ=diffusions&action=add";
		    },
		    edit: function (id, event) {
				const url = `./dsi.php?categ=diffusions&action=edit&id=${id}`;
				
				if (event && (event.ctrlKey || event.metaKey)) {
					// Ouvre dans un nouvel onglet si Ctrl (Windows) ou Cmd (Mac) est pressé
					window.open(url, "_blank");
				} else {
					// Sinon, redirige normalement
					document.location = url;
				}
		    },
		    getDiffusionStatus(idStatus) {
		    	let result = this.status.find((elem) => elem.id == idStatus);
		    	if(result && result.name) {
		    		return result.name;
		    	}
		    	return "";
		    },
			getDiffusionChannel(idTypeChannel = 0) {
		    	let result = this.channels.find((elem) => elem.id == idTypeChannel);
		    	if(result && result.name) {
		    		return result.name;
		    	}
		    	return "";
		    },
			duplicate : async function(idDiffusion) {
				let duplicate = await this.ws.post(this.$root.categ, "duplicate", {"id" : idDiffusion});
				if(!duplicate.error) {
					this.edit(duplicate.id);
					//this.$set(this.originalList, this.originalList.length, this.formatListElement(duplicate));
				}
			},
			deleteDiffusion : async function(diffusion) {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("diffusions", 'delete', diffusion);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						let i = this.originalList.findIndex((d) => d.id == diffusion.id);
						this.$delete(this.originalList, i);
					}
				}
			},
			sortTable: function(column) {
				if (column === this.sortColumn) {
					this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
				} else {
					this.sortColumn = column;
					this.sortDirection = 'asc';
				}
			},
			setFilter: function(list) {
				this.$set(this, "filterList", list);
			},
			checkAll: function($event) {
				if($event.target.checked) {
					for(let i = 0; i < this.sortedList.length; i++) {
						this.$set(this.batchActionList, i, this.sortedList[i].id);
					}
				}else {
					this.$set(this, "batchActionList", []);
				}
			},
			execAction: async function(action) {
				let response;

				if(this.batchActionList.length === 0) {
					return false;
				}

				switch(action) {
					case "deleteAll":
						if(confirm(this.messages.get('dsi', 'confirm_del'))) {
							response = await this.ws.post("diffusions", "deleteAll", { ids: this.batchActionList });
							if (!response.error) {
								this.filterList = this.filterList.filter(element => {
									if(this.batchActionList.includes(element.id)) {
										return false;
									}

									return true;
								})
								this.$set(this, "originalList", this.filterList);
								this.$set(this, "batchActionList", []);
								this.notif.info(this.messages.get('common', 'success_save'));
							} else {
								this.notif.error(this.messages.get('dsi', response.errorMessage));
							}
						}
						break;
						// TODO Other actions
				}
			}
		}
	}
</script>