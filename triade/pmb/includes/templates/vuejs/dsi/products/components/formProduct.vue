<template>
	<div id="form">
		<form action="#" method="POST" @submit.prevent="submit" class="dsi-form-product">
			
			<div class="dsi-form-group">
				<label class="etiquette" for="name">{{ messages.get('dsi', 'products_form_name') }}</label>
				<div class="dsi-form-group-content">
					<input type="text" id="name" name="name" v-model="product.name" required>
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="status">{{ messages.get('dsi', 'products_form_status') }}</label>
				<div class="dsi-form-group-content">
					<select id="status" name="status" v-model="product.numStatus" required>
						<option value="" disabled>{{ messages.get('dsi', 'products_form_choose_status') }}</option>
						<option v-for="(stat, index) in status" :key="index" :value="stat.id">
							{{ stat.name }}
						</option>
					</select>
				</div>
			</div>
			
			<div class="dsi-form-group">
				<label class="etiquette" for="diffusions">{{ messages.get('dsi', 'products_form_add_diffusion') }}</label>
				<div class="dsi-form-group-content">
					<select name="diffusions" v-model="diffusionSelector">
						<option disabled value="0">{{messages.get('dsi', 'products_form_choose_add_diffusion')}}</option>
						<option v-for="(diffusion, index) in filteredDiffusions" :value="diffusion.id" :key="index">{{diffusion.name}}</option>
					</select>
					<input :disabled="diffusionSelector == 0" type="button" class="bouton" value="+" @click="addDiffusion">
				</div>
			</div>
			<diffusionList :diffusionProduct="product.productDiffusions" :diffusions="diffusions" />
			<tags 
				v-if="product.id"
				:tags="product.tags"
				entity="products"
				:entity-id="product.id"></tags>
			<div class='row'>
				<br />
				<div class="left">
					<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
					<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
				</div>
				<template>
				    <div class="right">
					    <input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
			    	</div>
		    	</template>
	    	</div>
		</form>
	</div>
</template>

<script>
import diffusionList from "./diffusionList.vue";
import tags from "@dsi/components/tags.vue";
export default {
	props : ["product", "diffusions", "productdiffusion", "status"],
	components : {
		diffusionList,
		tags
	},
	data : function() {
		return {
			diffusionSelector : 0
		}
	},
	computed : {
		filteredDiffusions : function() {
			if(! this.product.productDiffusions ) {
				return this.diffusions;
			}
			return this.diffusions.filter((d) => {
				for(let productDiffusion of this.product.productDiffusions) {
					if(productDiffusion.num_diffusion == d.idDiffusion) {
						return false;
					}
				}
				return true;
			});
		}
	},
	created: function() {
		this.product.numStatus = this.product.numStatus ? this.product.numStatus : "";
	},
	methods: {
		cancel: function() {
			document.location = "./dsi.php?categ=products";
		},
		submit: async function() {
			let response = await this.ws.post('products', 'save', this.product);
			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				document.location = "dsi.php?categ=products&action=edit&id=" + response.id;
			}
		},
		del : async function() {
			if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				let response = await this.ws.post("products", 'delete', this.product);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					document.location = "./dsi.php?categ=products";
				}
			}
		},
		addDiffusion : function() {
			var productDiffusion = JSON.parse(JSON.stringify(this.productdiffusion));
			productDiffusion.num_diffusion = this.diffusionSelector;
			productDiffusion.num_product = this.product.idProduct;
			this.product.productDiffusions.push(productDiffusion);
			this.diffusionSelector = 0;
		},
		createDiffusion : function() {
			document.location = "./dsi.php?categ=diffusions&action=add";
		}
	}
}
</script>