<template>
	<div id="add">
		<div class="dsi-tabs">
		    <div class="dsi-tab-registers">
		        <button @click="switchTab(1)" :class="[tabActive == 1 ? 'active-tab bouton' : 'bouton']">
					{{ messages.get('dsi', 'diffusions') }}<b class="not-saved">{{ product.id != 0 ? "" : "*"}}</b>
				</button>
				<template v-if="product.id != 0">
					<button @click="switchTab(2)" :class="[tabActive == 2 ? 'active-tab bouton' : 'bouton']">
						{{ messages.get('dsi', 'recipient') }}<b class="not-saved">{{ product.numSubscriberList != 0 ? "" : "*"}}</b>
					</button>
					<button @click="switchTab(3)" :class="[tabActive == 3 ? 'active-tab bouton' : 'bouton']">
						{{ messages.get('dsi', 'triggers') }}<b class="not-saved">{{ product.events.length != 0 ? "" : "*"}}</b>
					</button>
				</template>
		    </div>
		    <div class="dsi-tab-bodies">
		        <div class="dsi-content" v-show="tabActive == 1">
		        	<formProduct :product="product" :diffusions="diffusions" :productdiffusion="productdiffusion" :status="status"></formProduct>
		        </div>
		        <div class="dsi-content" v-show="tabActive == 2">
					<formSubscriberListContainer :subscriberList="product.subscriberList" :id-entity="product.id"></formSubscriberListContainer>
		        </div>
		        <div class="dsi-content" v-show="tabActive == 3">
					<formProductEvent :product="product"></formProductEvent>
		        </div>
		    </div>
		</div>
	</div>
</template>

<script>
	import formProduct from "./formProduct.vue";
	import formSubscriberListContainer from "../../subscriberList/components/formSubscriberListContainer.vue";
	import formProductEvent from "./formProductEvent.vue";

	export default {
		props : ["product", "diffusions", "productdiffusion", "status"],
		data: function () {
			return {
			    tabActive: this.getTab()
			}
		},
		components : {
			formProduct,
			formSubscriberListContainer,
			formProductEvent
		},
		created : function() {
			this.getListners();
		},
		methods: {
			getListners : function() {
				this.$root.$on("updateSubscriberList", async (idSubscriberList)=>{
					this.product.numSubscriberList = idSubscriberList;

					let response = await this.ws.post('Products', 'save', this.product);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					}else {
						this.notif.info(this.messages.get('dsi', 'diffusion_form_subscriber_list_success'));
					}

					this.$set(this.product.subscriberList.source, "id",idSubscriberList);
					this.$set(this.product.subscriberList.source, "idSubscriberList",idSubscriberList);
				});
				this.$root.$on("replaceSubscriberList", ($event) => {
					this.$set(this.product, "numSubscriberList", $event.source.idSubscriberList);
					this.$set(this.product, "subscriberList", $event);
				});
			},
		    switchTab: function(tab) {
				if(this.product.id != 0) {
					this.tabActive = tab;
					sessionStorage.setItem("tabProductActive", JSON.stringify({id: this.product.id, tab: tab}));
				}
		    },
			getTab: function() {
				if(sessionStorage.getItem("tabProductActive")) {
					let obj = JSON.parse(sessionStorage.getItem("tabProductActive"));
					if(obj.id == this.product.id) {
						return obj.tab;
					}
				}
				return 1;
			}
		}
	}
</script>