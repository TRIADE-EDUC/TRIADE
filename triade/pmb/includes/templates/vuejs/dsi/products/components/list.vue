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
								{{ messages.get('dsi', 'item_name') }}
								<i :class="sortColumn === 'name' ? carretClass : 'fa fa-sort'" aria-hidden="true"></i>
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
						</tr>
					</thead>
					<tbody>
						<tr v-for="(product, index) in list" :key="index">
							<td>
								<input type="checkbox"
									:name="'batch-action-' + index"
									:id="'batch-action-' + index"
									:value="product.id"
									v-model="batchActionList">
							</td>
							<td style="cursor: pointer" @click="edit(product.id)">
								<a href="#" @click.prevent="edit(product.id)">{{ product.name }}</a>
							</td>
							<td>{{ product.nbSubscribers }}</td>
							<td>{{ product.lastDiffusion }}</td>
							<td>{{ product.status }}</td>
							<td>
								<span v-for="(tag, i) in product.tags" :key="i">
									{{ tag.name }}
									<span v-if="(i + 1) < product.tags.length">, </span>
								</span>
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
		props : ["list", "status"],
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
				this.filterList[i] = {
					"id": this.list[i].id,
					"name": this.list[i].name,
					"nbSubscribers": this.list[i].subscriberList.nbSubscribers,
					"status": this.getProductStatus(this.list[i].numStatus),
					"lastDiffusion": this.list[i].lastDiffusion,
					"tags": this.list[i].tags
				}
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
		    add: function () {
		        document.location = "./dsi.php?categ=products&action=add";
		    },
		    edit: function (id) {
		        document.location = `./dsi.php?categ=products&action=edit&id=${id}`;
		    },
			getProductStatus(idStatus) {
		    	let result = this.status.find((elem) => elem.id == idStatus);
		    	if(result && result.name) {
		    		return result.name;
		    	}
		    	return "";
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
							response = await this.ws.post("products", "deleteAll", { ids: this.batchActionList });
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