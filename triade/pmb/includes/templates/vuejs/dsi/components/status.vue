<template>
	<div id="statusList">
		<table>
			<tbody>
				<tr>
					<th>{{ messages.get("dsi", "status_name") }}</th>
				</tr>
				<tr v-for="(status, index) in list" :key="index" style="cursor: pointer" @click="edit(status)" @mouseover="hover = index" @mouseout="hover = -1" :class="[ index % 2 == 0 ? 'even' : 'odd', index == hover ? 'surbrillance' : '' ]">
					<td>
						<strong v-if="status.id == 1">{{ status.name }}</strong>
						<template v-else>{{ status.name }}</template>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="row">
	   		<input @click="add" class="bouton" type="button" :value="messages.get('dsi', 'add')"/>
		</div>
	</div>
</template>


<script>
	export default {
		props : ["list", 'categ'],
		
		data : function() {
			return {
				hover : -1
			}
		},
		
		methods : {
			add : function() {
				document.location = `./dsi.php?categ=${this.categ}&action=add`;
			},
			
			edit : function(status) {
				document.location = `./dsi.php?categ=${this.categ}&action=edit&id=${status.id}`;
			}
		}
	}
</script>