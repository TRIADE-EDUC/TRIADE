<template>
	<div>
		<select :title="$cms.getMessage('title_select_order')" v-model="newPlacingBefore" @change="emitChangeOrder">
			<option v-for="(child, key) in children_parent" v-if="isNotMe(child)" :value="child.semantic.id_tag">
				{{ $cms.getMessage('placing_before').replace('%s', child.name) }}
			</option>
			<option value="">{{ $cms.getMessage('put_in_last') }}</option>
		</select>
		<select :title="$cms.getMessage('title_select_parent')"	v-model="newParent" @change="emitChangeParent">
			<option v-for="(zone, key) in $cms.zoneList" :key="`parent_${key}`" :value="zone.semantic.id_tag" v-if="isNotMe(zone)">
				{{ zone.name }}
			</option>
		</select>
	</div>
</template>


<script>
	export default {
		name: "common_params",
		props: ["id_tag", "parent", "children_parent", "placing_before"],
	    data: function () {
	        return {
	            newParent: "",
	            newPlacingBefore: ""
	        }
	    },
	    computed: {
	        // element correspond a une page ou un  gabarit
			element: function() {
			    return (this.$root.container && this.$root.container.data && this.$root.container.data.item) ? this.$root.container.data.item : null;
			}
	    },
	    mounted: function() {
	        this.load();
	    },
	    beforeUpdate: function() {
	        this.load();
	    },
	    methods: {
	        load: function() {
		        this.newParent = this.parent;
		        this.newPlacingBefore = this.placing_before;
	        },
	        arrayOrderAlpha: function (list) {
		        list.sort((a, b) => {
		            var titleA = a.name.toLowerCase();
		            var titleB = b.name.toLowerCase();
		            if(titleA < titleB) { return -1; }
		            if(titleA > titleB) { return 1; }
		            return 0;
				});
		        return list;
			},
			isNotMe(element) {
				return (element && element.semantic.id_tag != this.id_tag);
			},
	        emitChangeParent: function() {
	            this.$emit('change_parent', this.newParent);
	        },
	        emitChangeOrder: function() {
	            this.$emit('change_order', this.newPlacingBefore);
	        },
	    }
	}
</script>