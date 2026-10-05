<template>
    <form>
        <div class="dsi-form-group" v-for="(type, key) in formData.carts" :key="key">
			<label class="etiquette" for="viewEntityTypeList">{{ formData.messages[key] }}</label>
			<div class="dsi-form-group-content">
				<select v-model="view.settings.cart[key]">
					<option value="0" disabled>{{formData.messages.cart_selector_empty_value}}</option>
                    <option v-for="(cart, index) in type" :key="index" :value="cart.idcaddie">{{cart.name}}</option>
				</select>
			</div>
		</div>
    </form>
</template>

<script>
export default {
    props : ["view"],
    data: function() {
        return {
            formData : []
        }
    },
    created : async function() {
        await this.getAdditionalData();
        if(! this.view.settings.cart) {
            this.$set(this.view.settings, "cart", {});
            for(let key in this.formData.carts) {
                this.$set(this.view.settings.cart, key, 0);
            }
        }
    },
    methods : {
        getAdditionalData: async function() {
            let response = await this.ws.get("views", `form/data/${this.view.type}/${this.view.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "formData", response);
            }
        },
    }
}
</script>