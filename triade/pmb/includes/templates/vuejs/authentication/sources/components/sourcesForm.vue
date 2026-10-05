<template>
	<div>
		<table class="sources-table">
			<tbody>
				<tr v-for="(sourceItem, index) in manifests_list" :key="index"
					:class="['source-item cursor-pointer', index%2 ? 'odd' : 'even', hover == index ? 'surbrillance' : '']"
					@mouseover="hover = index" @mouseout="hover = null"
					@click="clicked(sourceItem.name, sourceItem.params)">
					<td class="source-item-name">
						{{ sourceItem.name }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		props : ["action", "manifests_list"],
		data: function () {
			return {
			    hover: null
			}
		},
		components : {
		},
		computed: {
		    manifestList: function() {
		        let manifests_list = {};
		        for (let entityName in this.manifest) {
		            manifests_list[entityName] = this.manifest[entityName].sort(function(a, b) {
						if (a.label == b.label) {
							return 0;
						}
						return (a.label < b.label) ? -1 : 1;
					});
		        }
		        return manifests_list;
		    }
		},
		methods: {
		    clicked: function(sourceName, sourceParams) {
		        this.$emit('selected', {
		            'sourceName': sourceName,
		            'sourceParams': sourceParams
		        });
		    }
		}
	}
</script>