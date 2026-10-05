<template>
	<fieldset class="condition">
		<div class="condition-fields">
			<div class="condition-field">
				<label :for="idGlobal" class="portal-form-group-title cursor-pointer">
					{{ $cms.getMessage('portal_condition_global_label') }}
				</label>
				<input name="global" type="text" 
					:id="idGlobal" v-model.trim="global" 
					:placeholder="$cms.getMessage('portal_condition_global_label')"
					@change="change" required>
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
	            global: ""
	        }
	    },
	    computed: {
	        idGlobal: function() {
	            return `global_${this.index}`;
	        }
	    },
	    mounted: function() {
	        if (this.condition.data && this.condition.data.global) {	            
		        this.global = this.condition.data.global;
	        }
	    },
	    watch: {
	        "condition": function(newValue, oldValue) {
		        if (newValue.data && newValue.data.global) {	            
			        this.global = newValue.data.global;
		        }
	        }  
	    },
	    methods: {
	        remove: function() {
	            this.$emit('remove', this.index);
	        },
	        change: function() {
	            this.$emit('update', {global: this.global});
	        }
	    } 
	}
</script>