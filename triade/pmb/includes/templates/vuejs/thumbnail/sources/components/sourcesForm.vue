<template>
	<div>
		<template v-for="(entitySources, entityName) in sourcesList" >
			<accordion :title="messages.get('thumbnail', entityName)" :index="entityName" :key="entityName" :expanded="true">
				<table class="sources-table">
					<thead>
						<tr>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(sourceItem, index) in entitySources" :key="index"
							:class="['source-item cursor-pointer', index%2 ? 'odd' : 'even', hover == index ? 'surbrillance' : '']"
							@mouseover="hover = index" @mouseout="hover = null"
							@click="clicked(entityName, index, sourceItem.source)">
							<td class="source-item-name">
								{{ sourceItem.label }}
							</td>
						</tr>
					</tbody>
				</table>
			</accordion>
		</template>	
	</div>
</template>

<script>
	import accordion from "../../../common/accordion/accordion.vue";
	
	export default {
		props : ["action", "sources"],
		data: function () {
			return {
			    hover: null
			}
		},
		components : {
			accordion,
		},
		computed: {
		    sourcesList: function() {
		        let sourcesList = {};
		        for (let entityName in this.sources) {
		            sourcesList[entityName] = this.sources[entityName].sort(function(a, b) {
						if (a.label == b.label) {
							return 0;
						}
						return (a.label < b.label) ? -1 : 1;
					});
		        }
		        return sourcesList;
		    }
		},
		methods: {
		    clicked: function(entityType, index, sourceName) {
		        this.$emit('selected', {
		            'entityType': entityType,
		            'sourceIndex': index,
		            'sourceName': sourceName
		        });
		    }
		}
	}
</script>