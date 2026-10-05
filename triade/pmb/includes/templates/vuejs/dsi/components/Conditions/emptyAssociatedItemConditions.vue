<template>
    <div>
		<span>{{ getSummaryMsg() }}</span>
		<div v-if="isAggregatedView()">
			<div :class="classGroup">
				<label class="etiquette" for="conditions-associated-item">
					{{ messages.get('dsi', 'condition_empty_associated_view_label') }}
				</label>
	
				<div class="dsi-form-group-content">
					<select id="conditions-associated-item" name="conditions-associated-item" v-model="condition">
						<option value="">{{ messages.get('dsi', 'view-wysiwyg-no-condition') }}</option>
						<option v-for="(option, index) in viewList" :value="option.value" :key="index">
							{{ option.label }}
						</option>
					</select>

					<button 
						type="button" :title="messages.get('common', 'more_label')" 
						class="bouton" @click="addCondition"
						:disabled="condition == ''">

						<i class="fa fa-plus" aria-hidden="true"></i>
					</button>
				</div>
			</div>
	
			<div class="dsi-form-group"
				v-if="conditions.emptyAssociatedItem.views && conditions.emptyAssociatedItem.views.length > 0">
				
				<label><!-- Empty label don't remove --></label>
				<div class="dsi-form-group-content">
					<table class="conditions-associated-item-table">
						<tr v-for="(view, index) in viewListSelected" :key="index">
							<td>{{ view.label }}</td>
							<td>
								<button type="button" :title="messages.get('common', 'remove')"
									@click="removeCondition(index)" class="bouton">
	
									<i class="fa fa-times" aria-hidden="true"></i>
								</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div v-else :class="classGroup">
			<label>{{ messages.get('dsi', 'condition_empty_associated_simple_view_label') }}</label>
			<div class="dsi-form-group-content">
				<input type="checkbox"
					name="condition-simple-associated-item" 
					id="condition-simple-associated-item"
					ref="condition-simple-associated-item"
					@change="changeCondition($event)">
			</div>
		</div>
    </div>
</template>

<script>
    export default {
        props: ["conditions", "context"],
		data: function () {
			return {
                condition: ""
			}
		},
        created: function () {
			if (!this.conditions.emptyAssociatedItem) {
            	this.$set(this.conditions, "emptyAssociatedItem", { views: [] });
			}
        },
		beforeUpdate: function () {
			if (!this.conditions.emptyAssociatedItem) {
            	this.$set(this.conditions, "emptyAssociatedItem", { views: [] });
			}
		},
		watch: {
			conditions: function() {
				if (!this.conditions.emptyAssociatedItem) {
					this.$set(this.conditions, "emptyAssociatedItem", { views: [] });
				}

				if(this.$refs["condition-simple-associated-item"] === undefined) {
					return;
				}

				if(this.conditions.emptyAssociatedItem.views && this.conditions.emptyAssociatedItem.views.length > 0) {
					this.$refs["condition-simple-associated-item"].checked = true;
				} else {
					this.$refs["condition-simple-associated-item"].checked = false;
				}

			}
		},
        computed: {
            viewList: function () {
				const list = this.getViewList(this.$root.diffusion.view ?? {});

				return list.filter((view) => {
					const found = this.conditions.emptyAssociatedItem.views.find(viewSelected => viewSelected == view.value);
					return found === undefined;
				});
        	},
			viewListSelected: function () {
				const list = this.getViewList(this.$root.diffusion.view ?? {});

				return list.filter((view) => {
					const found = this.conditions.emptyAssociatedItem.views.find(viewSelected => viewSelected == view.value);
					return found !== undefined;
				});
			},
			classGroup: function() {
				if(this.context == "wysiwyg") {
					return "dsi-form-group dsi-form-wysiwyg";
				}

				return "dsi-form-group";
			}
        },
        methods: {
			getViewList: function (view) {
				let viewList = [];

				if (view && Object.keys(view).length && view.id && (!view.childs || view.childs.length <= 0)) {
					viewList.push({
						value: view.id,
						label: view.name
					});
				}

				if (view.childs && view.childs.length > 0) {
					for (const viewChild of view.childs) {
						viewList = [...viewList, ...this.getViewList(viewChild)];
					}
				}

				return viewList;
        	},
			getSummaryMsg: function() {
				if(this.context == "trigger") {
					return this.messages.get('dsi', 'event_condition_associated_view_summary');
				}
				
				return this.messages.get('dsi', 'wysiwyg_condition_associated_view_summary');
			},
			isAggregatedView: function() {
				if(! this.$root.diffusion) {
					return false;
				}
				const view = this.$root.diffusion.view;
				if(!view) {
					return false;
				}
				return this.Const.views.aggregatedViewsIds.includes(view.type);
			},
			addCondition: function() {
				const viewSelected = this.viewList.find(view => view.value == this.condition);
				if (viewSelected !== undefined) {
					this.conditions.emptyAssociatedItem.views.push(viewSelected.value);
					this.condition = "";
				}
			},
			removeCondition: function(index) {
				this.conditions.emptyAssociatedItem.views.splice(index, 1);
			},
			changeCondition: function(event) {
				if(event.target.checked) {
					this.condition = this.viewList[0].value;
					this.addCondition();

					return;
				}

				this.removeCondition(0);
			}
        }
    }
</script>