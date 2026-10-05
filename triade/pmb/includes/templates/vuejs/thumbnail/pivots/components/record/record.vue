<template>
	<div class="grid-2-col">
		<div class="pivot">
			<!-- TODO: Gérer le multi pivot  -->
			<component v-if="componentPivot" :is="componentPivot" :pivot="pivot" @selected="showForm($event)" @cleanForm="cancel"></component>
		</div>
		<div class="pivot-form">
			<h3>{{ data.label }}</h3>
			<component v-if="pivot && formData && !loadComponent"
				:is="componentPivotForm" 
				:pivot="pivot" 
				:sources="data.sources" 
				:formData="formData"
				@cancel="cancel">
			</component>
			<div v-if="loadComponent && !formData">
				<img :src="images.get('patience.gif')" :alt="messages.get('common', 'wait')" :title="messages.get('common', 'wait')">
			</div>
		</div>
	</div>
</template>

<script>
	import record_basic_pivot from "./pivots/record_basic_pivot.vue"
	import record_basic_pivot_form from "./pivots/record_basic_pivot_form.vue";
	
	export default {
		props : ["action", "data"],
		data: function () {
			return {
			    loadComponent: false,
			    formData: null
			}
		},
		components : {
		    record_basic_pivot,
		    record_basic_pivot_form
		},
		computed: {
		    pivot: function() {
		        return this.data.pivots[0] ? this.data.pivots[0] : null;
		    },
		    componentPivot: function() {
		        return this.pivot ? this.pivot.component : null;
		    },
			componentPivotForm: function() {
			    return this.componentPivot ? `${this.componentPivot}_form` : null;
		    }
		},
		updated: function() {
			if (typeof domUpdated	=== "function") {
			    domUpdated();
			}
		},
		methods: {
		    setFormData: function(data) {
		        this.formData = data
		    },
		    cancel: function() {
		        this.formData = null;
		    },
			showForm: async function(item) {
			    
				this.loadComponent = true;
				this.cancel();
				
				if (item !== null) {				    
					const response = await this.ws.post("pivot", `${this.data.type}/sources`, {
					    pivot: item.pivot
					});
					
					if (response.error) {
				        console.error(response.errorMessage);
					} else {
						this.setFormData({
						    ...item,
						    "sources": response.sources ?? []
						})
					}
				}
				this.loadComponent = false;
			}
		}
	}
</script>