<template>
	<div >
		<table>
			<tr>
				<th>{{ pmb.getMessage("animation", "admin_type_de_prix") }}</th>
				<th>{{ pmb.getMessage("animation", "admin_type_default_price") }}</th>
				<th>{{ pmb.getMessage("animation", "animations_perso") }}</th>
			</tr>
			<tr v-for="(type, index) in pricetypes" 
				:key='index' 
				style='cursor: pointer' 
				@mouseover="hover = index "
				@mouseout="hover = -1 "
				:class="[ index%2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '' ]"
			>
				<td @click="update(type.id)" >
					{{ type.name }}
				</td>
				<td @click="update(type.id)" >
					{{ type.defaultValue }}
				</td>
				<td>
					<template v-for="(custom, indexCustom) in type.customFields">
						<label>{{ custom.customField.titre }}</label><br/>
					</template>
   					<input  @click="addCustomField(type.id)" class="bouton" type="button" :value="pmb.getMessage('animation', 'animations_perso_edit')"/>
				</td>
			</tr>
		</table>
		<div class='row'>
	   		<input  @click="newType" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_animations_priceTypes_add')"/>
		</div>
	</div>
</template>


<script>
	export default {
		props : ["pmb","pricetypes"],
		data:function(){
			return {
				hover:-1
			}
		},
		methods:{
			newType : function() {
				document.location = './admin.php?categ=animations&sub=priceTypes&action=add';
			},
			update : function(id) {
				document.location = './admin.php?categ=animations&sub=priceTypes&action=edit&id=' + id;
			},
			addCustomField : function(id){
				document.location = './admin.php?categ=animations&sub=priceTypesPerso&type_field=anim_price_type&numPriceType=' + id;
			}
		}
	}
</script>