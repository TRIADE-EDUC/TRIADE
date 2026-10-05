<template>
	<div class="portal-form-group">
		<label for="conditions" class="portal-form-group-title cursor-pointer">
			{{ $cms.getMessage('portal_conditions_label') }}
		</label>
		<div>
			<select id="conditions" name="condition" v-model="newCondition" @change="add">
				<option value="" disabled>
					{{ $cms.getMessage('portal_condition_default_option') }}
				</option>
				<option v-for="(condition, index) in $cms.conditions" :value="condition.namespace" :key="`condition_${index}`">
					{{ condition.label }}
				</option>
			</select>
		</div>
		<div class="conditions">
			<component 
				v-for="(condition, index) in conditions" :key="index" 
				:is="getComponent(condition)" 
				:condition="condition" :index="index"
				@remove="$emit('remove', $event)"
				@update="$emit('update', $event, index)">
			</component>
		</div>
	</div>
</template>

<script>
	import ConditionEnvModel from './conditions/ConditionEnvModel.vue';
	import ConditionOpacViewModel from './conditions/ConditionOpacViewModel.vue';
	
	export default {
	    props: ['conditions'],
	    data: function() {
	        return {
	            newCondition: ""
	        }
	    },
	    components: {
	        ConditionEnvModel,
	        ConditionOpacViewModel
	    },
	    methods: {
	        add: function() {
	            this.$emit("add", this.newCondition);
	            this.newCondition = "";
	        },
	        getComponent: function (condition) {
	            const component = condition['class'].replace('Pmb\\CMS\\Models\\', '');
	            if (!this.$options.components[component]) {
	                throw `component ${component} not imported`;
	            }
	            return component;
	        }
	    }
	}
</script>