<template>
	<div id="statusList">
		<table>
			<tbody>
				<tr>
					<th>{{ pmb.getMessage("animation", "admin_status") }}</th>
				</tr>
				<tr v-for="(status, index) in statuslist" :key="index" style="cursor: pointer" @click="edit(status.id_status)" @mouseover="hover = index" @mouseout="hover = -1" :class="[ index % 2 == 0 ? 'even' : 'odd', index == hover ? 'surbrillance' : '' ]">
					<td>
						<strong v-if="status.id_status == 1">{{ status.label }}</strong>
						<template v-else>{{ status.label }}</template>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="row">
	   		<input @click="newStatus" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_add_status')"/>
		</div>
	</div>
</template>


<script>
	export default {
		props : ["pmb", "statuslist"],
		
		data : function() {
			return {
				hover : -1
			}
		},
		
		methods : {
			newStatus : function() {
				document.location = './admin.php?categ=animations&sub=status&action=add';
			},
			
			edit : function(id) {
				document.location = './admin.php?categ=animations&sub=status&action=edit&id=' + id;
			}
		}
	}
</script>