<template>
	<div>
		<div class="portal-form-group" v-if="isEditable">
			<label v-if="isForm" for="zone-semantic" 
				class="portal-form-group-title cursor-pointer">
				{{ label }}
			</label>
			<select id="zone-class-semantic" name="zone_class_semantic" v-model="selectedSemantic" @change="changeSemantic" required>
				<option v-for="(semantic, key) in $cms.class_semantic" :key="key" :value="semantic.class_name">
					{{ semantic.label }}
				</option>
			</select>
		</div>
		 
		<component v-if="componentName"
			:is="componentName"
			:semantic="semantic"
			@update="$emit('update', $event)">
		</component>
	</div>
</template>


<script>
	import HtmlSemantic from './HtmlSemantic.vue';

	export default {
	    props: ['semantic', 'editable'],
		data: function() {
		    return {
		        selectedSemantic: "",
		        isEditable: true,
		        isForm: true
		    }
		},
	    components: {
	        HtmlSemantic
	    },
	    mounted: function() {
	        if (typeof this.editable != "undefined") {
	            this.isEditable = this.boolval(this.editable);
	        }
	        
	        if (!this.semantic || !this.semantic.class) {	            
		        this.selectedSemantic = this.firstComponent;
		        this.changeSemantic();
	        } else {
		        this.selectedSemantic = this.getClassName(this.semantic['class']);	            
	        }
	    },
	    watch: {
	        "semantic": function(newValue, oldValue) {
		        if (!newValue || !newValue.class) {	            
			        this.selectedSemantic = this.defaultSemantic;
			        this.changeSemantic();
		        } else {
			        this.selectedSemantic = this.getClassName(newValue['class']);
		        }
	        }  
	    },
	    computed: {
	        defaultStructure:  function() {
	            if (this.componentName) {	                
		            return {
		        	    id: 0,
		        	    "class": "Pmb\\CMS\\Semantics\\" + this.componentName,
						classes: [],
		        	    id_tag: "",
		        	    tag: ""
		            }
	            }
				return null;	            
        	},
	        label: function() {
	            return this.$cms.getMessage('zone_class_semantic') ?? "";
	        },
	        firstComponent: function() {
	            const components = Object.keys(this.$options.components);
	            return components[0] ?? "";
	        },
			componentName: function () {
	            if (!this.selectedSemantic) {
	                return "";
	            }
	            
	            const component = this.getClassName(this.selectedSemantic);
	            if (!this.$options.components[component]) {
	                throw `Component ${component} not imported`;
	            }
	            return component;
	        }
	    },
		methods: {
		    boolval(bool) {
		        return (bool == "true" || bool == true) ? true : false;
		    },
		    getClassName: function(namespace) {
		        return namespace.replace('Pmb\\CMS\\Semantics\\', '');
		    },
		    changeSemantic: function() {
		        if (this.defaultStructure) {		            
		            this.$emit("change", this.defaultStructure);
		        }
		    }
		}
	}
</script>