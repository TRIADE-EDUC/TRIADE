<template>
	<table class="uk-table uk-table-small uk-table-striped uk-table-middle" v-if="diffusionProduct.length">
		<thead>
			<tr>
				<th>{{ messages.get('dsi', 'product_diffusion_diffusion_name') }}</th>
				<th>{{ messages.get('dsi', 'product_diffusion_last_activation') }}</th>
				<th>{{ messages.get('dsi', 'product_diffusion_active') }}</th>
				<th>{{ messages.get('dsi', 'product_diffusion_activate') }}</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="(dp, index) in diffusionProduct" :key="index">
				<td>{{getDiffusion(dp.num_diffusion).name}}</td>
				<td>{{getDiffusionDate(index)}}</td>
				<td><input type="checkbox" v-model="dp.active" /></td>
				<td>
					<input type="button" class="bouton" :value="messages.get('dsi', 'product_diffusion_delete')" @click="deleteProductDiffusion(index)" />
				</td>
			</tr>
		</tbody>
	</table>
</template>

<script>
export default {
	props : ["diffusionProduct", "diffusions"],
	methods : {
		getDiffusionDate : function(i) {
			let date = new Date(this.diffusionProduct[i].last_diffusion);
			if(this.diffusionProduct[i].last_diffusion === null || date == "Invalid Date"){
				return this.messages.get('dsi', 'product_diffusion_never_active');
			}
			return date.toLocaleDateString() + " " + date.toLocaleTimeString();
		},
		getDiffusion : function(idDiffusion) {
			return this.diffusions.find(diffusion => diffusion.idDiffusion == idDiffusion);
		},
		deleteProductDiffusion(i) {
			if(confirm(this.messages.get('dsi', 'product_diffusion_confirm_delete'))) {
				this.ws.post("products", 'deleteProductDiffusion', this.diffusionProduct[i]);
				this.diffusionProduct.splice(i, 1);
			}
		}
	}
}
</script>




