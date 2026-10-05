<template>
	<div class="portal-form-group">
		<label for="zone-semantic" class="portal-form-group-title cursor-pointer">
			{{ $cms.getMessage("zone_semantic") }}
		</label>
		<select id="zone-semantic" name="zone_semantic" v-model="tag" @change="update" required>
			<option v-for="(tag, key) in $cms.semantic" :key="key" :value="tag">{{ tag }}</option>
		</select>
	</div>
</template>

<script>
	export default {
	    props: ['semantic'],
		data: function() {
		    return {
		        tag: ""
		    }
		},
	    mounted: function() {
	        if (this.semantic && this.semantic.tag) {	            
		        this.tag = this.semantic.tag;
	        } else {
		        this.tag = this.firstTag;
		        this.update();
	        }
	    },
	    watch: {
	        "semantic": function(newValue, oldValue) {
		        if (newValue && newValue.tag) {	            
			        this.tag = newValue.tag;
		        } else {           
			        this.tag = this.firstTag;
			        this.update();
		        }
	        }  
	    },
	    computed: {
	        firstTag: function() {
	            return this.$cms.semantic[0] ?? "";
	        }
	    },
		methods: {
		    update:  function () {
		        var semantic = this.$cms.cloneObject(this.semantic);
		        semantic.tag = this.tag;
		        this.$emit("update", semantic);
		    }
		}
	}
</script>