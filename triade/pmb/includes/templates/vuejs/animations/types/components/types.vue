<template>
	<div id="typesList">
		<table>
			<tbody>
				<tr>
					<th>{{ pmb.getMessage("animation", "admin_types") }}</th>
				</tr>
				<tr v-for="(type, index) in typeslist" :key="index" style="cursor: pointer" @click="edit(type.id_type)" @mouseover="hover = index" @mouseout="hover = -1" :class="[ index % 2 == 0 ? 'even' : 'odd', index == hover ? 'surbrillance' : '' ]">
					<td>
						<strong v-if="type.id_type == 1">{{ type.label }}</strong>
						<template v-else>{{ type.label }}</template>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="row">
	   		<input @click="newType" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_add_types')"/>
		</div>
	</div>
</template>


<script>
	export default {
		props : ["pmb", "typeslist"],
		
		data : function() {
			return {
				hover : -1
			}
		},
		
		methods : {
			newType : function() {
				document.location = './admin.php?categ=animations&sub=types&action=add';
			},
			
			edit : function(id) {
				document.location = './admin.php?categ=animations&sub=types&action=edit&id=' + id;
			}
		}
	}
</script>