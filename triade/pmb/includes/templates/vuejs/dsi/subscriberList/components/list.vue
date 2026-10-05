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
							<th @click="sortTable('name')" style="cursor: pointer;">
								{{ messages.get('dsi', 'subscriber_list_name') }}
								<i :class="sortColumn === 'name' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th>{{ messages.get('dsi', 'subscriber_list_search') }}</th>
							<th @click="sortTable('nbSubscribers')" style="cursor: pointer;">
								{{ messages.get('dsi', 'subscriber_list_subscribers') }}
								<i :class="sortColumn === 'nbSubscribers' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
							</th>
							<th>{{ messages.get('dsi', 'dsi_tag_list') }}</th>
							<th class="dsi-table-right"><span>{{ messages.get('dsi', 'actions') }}</span></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(subscriberList, index) in list" :key="index">
							<td>
								<input type="checkbox"
									:name="'batch-action-' + index"
									:id="'batch-action-' + index"
									:value="subscriberList.source.id"
									v-model="batchActionList">
							</td>
							<td style="cursor: pointer" @click="edit(subscriberList.source.id)">
								<a href="#" @click.prevent="edit(subscriberList.source.id)">{{ subscriberList.source.name }}</a>
							</td>
							<td v-html="subscriberList.source.source.selector.searchInput"></td>
							<td>{{ subscriberList.nbSubscribers }}</td>
							<td>
								<span v-for="(tag, i) in subscriberList.tags" :key="i">
									{{ tag.name }}
									<span v-if="(i + 1) < subscriberList.tags.length">, </span>
								</span>
							</td>
							<td class="dsi-table-right">
								<button type="button" class="bouton" @click="exportSubscriberList(subscriberList.source.id)">{{ messages.get('dsi', 'model_export') }}</button>
								<button type="button" class="bouton" @click="edit(subscriberList.source.id)">{{ messages.get('dsi', 'edite') }}</button>
								<button type="button" class="bouton" @click="duplicate(subscriberList.source.id)">{{ messages.get('dsi', 'dsi_duplicate') }}</button>
								<button type="button" class="bouton btnDelete" @click="deleteSubscriberList(subscriberList)">{{ messages.get('dsi', 'del') }}</button>
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
		<div class="dsi-list-actions">
			<div>
				<input type="button" class="bouton" :value="messages.get('dsi', 'add_model')" @click="add">
			</div>

			<modelImport @importModel="importSubscriberList"></modelImport>
		</div>
	</div>
</template>

<script>
	import filterList from "../../components/filterList.vue";
	import paginationList from "../../components/paginationList.vue";
	import modelImport from "../../components/modelImport.vue";

	export default {
		props : ["list"],
		components: {
			paginationList,
			filterList,
			modelImport
		},
		data: function () {
			return {
				sortColumn: 'name',
      			sortDirection: 'asc',
				filterFields: [
					{
						name : "name",
						label : "subscriber_list_name",
						type : "text"
					},
					{
						name : "nbSubscribers",
						label : "subscriber_list_subscribers",
						type : "number"
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
				console.log(element)
				return {
					"id": element.source.id,
					"name": element.name,
					"nbSubscribers": element.nbSubscribers,
					"source": element.source,
					"tags": element.source.tags
				}
			},
		    add: function () {
		        document.location = "./dsi.php?categ=subscriber_list&action=add";
		    },
		    edit: function (id) {
		        document.location = `./dsi.php?categ=subscriber_list&action=edit&id=${id}`;
		    },
			duplicate : async function(idSubscriberList) {
				let duplicate = await this.ws.post(this.$root.categ, "duplicate", {"id" : idSubscriberList});
				if(!duplicate.error) {
					console.log(duplicate)
					this.edit(duplicate.source.id);
					//this.$set(this.originalList, this.originalList.length, this.formatListElement(duplicate));
				}
			},
			exportSubscriberList: async function(idSubscriberList) {
				window.open(`./rest.php/dsi/subscriberList/export/${idSubscriberList}`, "__blank");
			},
			importSubscriberList: function(importedSubscriberList) {
				this.$set(this.originalList, this.originalList.length, this.formatListElement(importedSubscriberList));
			},
			deleteSubscriberList : async function(subscriberList) {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("subscriberList", 'delete', subscriberList);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						let i = this.originalList.findIndex((d) => d.source.id == subscriberList.source.id);
						this.$delete(this.originalList, i);
					}
				}
			},
			sortTable(column) {
				if (column === this.sortColumn) {
					this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
				} else {
					this.sortColumn = column;
					this.sortDirection = 'asc';
				}
			},
			setFilter : function(list) {
				this.$set(this, "filterList", list);
			},
			checkAll: function($event) {
				if($event.target.checked) {
					for(let i = 0; i < this.sortedList.length; i++) {
						this.$set(this.batchActionList, i, this.sortedList[i].source.id);
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
							response = await this.ws.post("subscriberList", "deleteAll", { ids: this.batchActionList });
							if (!response.error) {
								this.filterList = this.filterList.filter(element => {
									if(this.batchActionList.includes(element.source.id)) {
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