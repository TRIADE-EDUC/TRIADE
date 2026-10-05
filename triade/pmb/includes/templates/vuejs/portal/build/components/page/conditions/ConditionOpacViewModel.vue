<template>
	<fieldset class="condition">
		<div class="condition-fields">
			<div class="condition-field">
				<label :for="idSelect" class="portal-form-group-title cursor-pointer">
					{{ $cms.getMessage('portal_condition_view_label') }}
				</label>
				<select v-if="opac_views.length > 0" :id="idSelect" v-model="opac_view" @change="change" required>
					<option value="" disabled>
						{{ $cms.getMessage('portal_condition_view_default_option') }}
					</option>
					<option v-for="(opac_view, index) in opac_views" :value="opac_view.value" :key="index">
						{{ opac_view.label }}
					</option>
				</select>
				<p v-else>{{ $cms.getMessage('portal_condition_view_no_view') }}</p>
			</div>
			<button type="button" class="bouton remove" @click="remove">X</button>
		</div>
	</fieldset>
</template>

<script>

	export default {
	    props: ['condition', 'index'],
	    data: function() {
	        return {
	            opac_view: "",
	            opac_views: []
	        }
	    },
	    created: async function() {
	    	this.opac_views = await this.$cms.model.getOpacViews();
	    },
	    computed: {
	        idSelect: function() {
	            return `select_view_${this.index}`;
	        }
	    },
	    mounted: function() {
	        if (this.condition.data && this.condition.data.opac_view) {	            
		        this.opac_view = this.condition.data.opac_view;
	        }
	    },
	    watch: {
	        "condition": function(newValue, oldValue) {
		        if (newValue.data && newValue.data.opac_view) {	            
			        this.opac_view = newValue.data.opac_view;
		        }
	        }  
	    },
	    methods: {
	        remove: function() {
	            this.$emit('remove', this.index);
	        },
	        change: function() {
	            this.$emit('update', {opac_view: this.opac_view});
	        }
	    } 
	}
</script>