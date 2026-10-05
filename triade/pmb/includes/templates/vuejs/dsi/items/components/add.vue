<template>
	<div id="add">
		<div class="dsi-tabs">
			<div class="dsi-tab-registers">
				<span v-for="(element, index) in tabItems" :key="element.uid">
					<button @click="switchTab(element.uid)" type="button"
						:class="[tabActive == element.uid ? 'active-tab bouton' : 'bouton']">

						{{ element.item.name != "" ? element.item.name : "Sans nom(" + (index) + ")" }}

						<span type="button" v-if="index > 0" style="cursor: pointer"
							@click.stop="closeNewTab(element.uid, element.parent)"
							:title="messages.get('dsi', 'view_wysiwyg_close_tab')">

							<i class="fa fa-times" aria-hidden="true"></i>
						</span>
					</button>

					<!-- hack pour les espaces entre les boutons -->
					<span></span>
				</span>

			</div>
			<div class="dsi-tab-bodies">
				<div v-for="(element, index) in tabItems" :key="element.uid" class="dsi-content"
					v-show="tabActive == element.uid">
					<formItem :types="types" :item="element.item" :is_model="is_model" :tab_index="index"
						:rootItem="index === 0 ? '' : item" :uid="element.uid" :parentUid="element.parent"
						:action="element.action" :viewType="viewType" @saveDiffusionItem="saveDiffusionItem"
						:is-only-for-aggregator="isOnlyForAggregator">
					</formItem>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import formItem from "./formItem.vue";
export default {
	props: ["types", "item", "is_model", "viewType", "isOnlyForAggregator"],
	data: function () {
		return {
			tabActive: [],
			mainUid: "",
			tabItems: [],
			uidColors: {}
		}
	},
	components: {
		formItem,
	},
	created: function () {
		this.item.model = this.is_model;

		// Insert l'item principal dans les tabs
		this.mainUid = this.item.uid ?? this.genUid();
		this.tabItems.push({ uid: this.mainUid, parent: "", item: this.item, });
		this.switchTab(this.mainUid);

		// Déclaration des listeners
		this.$root.$on("openNewTab", this.openNewTab)
		this.$root.$on("closeNewTab", this.closeNewTab)
	},
	methods: {
		// Change la tab active
		switchTab: function (uid) { this.tabActive = uid; },

		// Evènement pour ouvrir une nouvelle tab
		openNewTab: async function (uid, item) {
			if(! item || ! item.uid) {
				var newUid = this.genUid();
			} else {
				var newUid = item.uid;
			}
			if (!uid) {
				//Si aucun id de donné on prend celui du premier
				uid = this.tabItems[0].uid;
			}
			if (item !== null && item.id) {
				//On vérifie que l'onglet n'est pas déjà ouvert
				let i = this.tabItems.findIndex((tab) => tab.item.id == item.id);
				if (i !== -1) {
					this.tabActive = this.tabItems[i].uid;
					return;
				}
			}
			this.tabItems.push({
				uid: newUid,
				parent: uid,
				item: item !== null ? item : await this.getEmptyItem(),
				action: item !== null ? "edit" : "add"
			});

			this.switchTab(newUid)
		},

		// Evènement pour fermer une tab
		closeNewTab: async function (uid, parentUid) {
			this.tabItems = this.tabItems.filter((obj) => { return obj.uid !== uid && obj.parent !== uid; });
			this.tabActive = parentUid;
		},

		closeRecursiveTab: function (uid) {
			for (const tab in this.tabItems) {
				if (tab.uid === uid) {
					delete this.tabItems[uid];
				}
				if (tab.parent === uid) {
					delete this.tabItems[tab.uid];
					this.closeRecursiveTab(tab.uid);
				}
			}
		},

		// Récupère une instance d'item vide
		getEmptyItem: async function () {
			let response = await this.ws.get("items", 'getEmptyInstance');

			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				return response;
			}
			return null;
		},

		// Génère un identifiant unique
		genUid() {
			const uid = Date.now().toString(36) + Math.random().toString(36).substring(2);
			this.uidColors[uid] = this.genColor();

			return uid;
		},

		genColor() {
			return Math.floor(Math.random() * 16777215).toString(16);
		},

		saveDiffusionItem(idItem) {
			this.$emit("saveDiffusionItem", idItem);
		}
	}
}
</script>