<template>
	<div class="dsi-form-diffusion-container">
		<p class="diffusion-last-send">{{ messages.get('dsi', 'diffusion_last_send').replace('%s', diffusion.lastDiffusion) }}</p>

		<form action="#" method="POST" @submit.prevent="submit" class="dsi-form-diffusion">

			<div class="dsi-form-group">
				<label class="etiquette" for="name">{{ messages.get('dsi', 'diffusion_form_name') }}</label>
				<div class="dsi-form-group-content">
					<input type="text" id="name" name="name" v-model="diffusion.name" required>
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="opac_name">{{ messages.get('dsi', 'diffusion_opac_name') }}</label>
				<div class="dsi-form-group-content">
					<input type="text" id="name" name="opac_name" v-model="diffusion.settings.opacName" />
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="status">{{ messages.get('dsi', 'diffusion_form_status') }}</label>
				<div class="dsi-form-group-content">
					<select id="status" name="status" v-model="diffusion.numStatus" required>
						<option value="" disabled>{{ messages.get('dsi', 'diffusion_form_choose_status') }}</option>
						<option v-for="(stat, index) in status" :key="index" :value="stat.id">
							{{ stat.name }}
						</option>
					</select>
				</div>
			</div>

			<!-- <div class="dsi-form-group">
				<label class="etiquette" for="history-active">{{ messages.get('dsi', 'dsi_activated_history') }}</label>
				<div class="dsi-form-group-content">
					<div class="dsi-checkbox-group">
						<input type="checkbox" class="switch" name="history-active" id="history-active" v-model="diffusion.settings.history_activated">
						<label for="history-active">&nbsp;</label>
					</div>
				</div>
			</div> -->

			<div class="dsi-form-group">
				<label class="etiquette" for="nb_history_saved">{{ messages.get('dsi', 'dsi_nb_history_saved') }}</label>
				<div class="dsi-form-group-content">
					<input type="number" name="nb_history_saved" id="nb_history_saved" min="1" v-model="diffusion.settings.nb_history_saved" required>
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="automatic-active">{{ messages.get('dsi', 'diffusion_automatic_send') }}</label>
				<div class="dsi-form-group-content">
					<div class="dsi-checkbox-group">
						<input type="checkbox" class="switch" name="automatic-active" id="automatic-active" v-model="diffusion.automatic">
						<label for="automatic-active">&nbsp;</label>
					</div>
				</div>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="descriptors">{{ messages.get('dsi', 'dsi_form_desc') }}</label>
				<descriptors
					class="dsi-form-group-content"
					:descriptors="diffusion.descriptors"
					@update="updateDescriptors">
				</descriptors>
			</div>

			<div class="dsi-form-group">
				<label class="etiquette" for="opac-active">{{ messages.get('dsi', 'diffusion_opac_visibility') }}</label>
				<div class="dsi-form-group-content">
					<div class="dsi-checkbox-group">
						<input type="checkbox" class="switch" name="opac-active" id="opac-active" v-model="diffusion.settings.opacVisibility">
						<label for="opac-active">&nbsp;</label>
					</div>
				</div>
			</div>
			<div class="dsi-form-group" v-if="diffusion.settings.opacVisibility && empr.categ.length">
				<label class="etiquette" for="opac-visibility-categ">{{ messages.get('dsi', 'form_diffusions_opac_visibility_categ') }}</label>
				<div class="dsi-form-group-content">
					<!-- <select multiple v-model="diffusion.settings.opacVisibilityStatus">
						<option v-for="(status, i) in empr.status" :key="i" :value="status.value">{{status.label}}</option>
					</select> -->
					<select multiple v-model="diffusion.settings.opacVisibilityCateg" name="opac-visibility-categ">
						<option v-for="(categ, i) in empr.categ" :key="i" :value="categ.value">{{categ.label}}</option>
					</select>
					<button :title="messages.get('common', 'remove')" class="bouton" @click.prevent="$set(diffusion.settings, 'opacVisibilityCateg', [])">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
			</div>
			<div class="dsi-form-group" v-if="diffusion.settings.opacVisibility && empr.groups.length">
				<label class="etiquette" for="opac-visibility-groups">{{ messages.get('dsi', 'form_diffusions_opac_visibility_groups') }}</label>
				<div class="dsi-form-group-content">
					<select multiple v-model="diffusion.settings.opacVisibilityGroups" name="opac-visibility-groups">
						<option v-for="(group, i) in empr.groups" :key="i" :value="group.value">{{group.label}}</option>
					</select>
					<button :title="messages.get('common', 'remove')" class="bouton" @click.prevent="$set(diffusion.settings, 'opacVisibilityGroups', [])">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>
			</div>
			<!--
			<div class="dsi-form-group">
				<label class="etiquette" for="active">{{ messages.get('dsi', 'form_active') }}</label>
				<div class="dsi-form-group-content">
					<div class="dsi-checkbox-group">
						<input type="checkbox" class="switch" name="active" id="active" v-model="diffusion.active">
						<label for="active">&nbsp;</label>
					</div>
				</div>
			</div>
			 -->

			<div class="dsi-list">
				<p>{{ messages.get('dsi', 'diffusion_form_products') }}</p>
				<div v-if="diffusion.diffusionProducts" class="dsi-cards">
					<span v-if="diffusion.diffusionProducts.length == 0">{{ messages.get("dsi", "diffusion_form_empty_products") }}</span>
					<div class="dsi-card" v-for="(diffusionProduct, i) in diffusion.diffusionProducts" :key="i">
						<a :href="$root.url_base + 'dsi.php?categ=products&action=edit&id=' + diffusionProduct.num_product">
							<p>{{ getProductName(diffusionProduct.num_product).name }}</p>
						</a>
					</div>
				</div>
			</div>
			<tags v-if="diffusion.id"
				:tags="diffusion.tags"
				entity="diffusions"
				:entity-id="diffusion.id"></tags>
			<div class='row'>
				<br />
				<div class="left">
					<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
					<input type="submit" class="bouton" :value="messages.get('common', 'submit')">
					<input v-if="canManuallySend" type="button" class="bouton" :value="manuallySendLabel" :title="manuallySendTitle" @click="sendDiffusionManually">
				</div>
				<template v-if="diffusion.id != 0">
				    <div class="right">
					    <input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
			    	</div>
		    	</template>
	    	</div>
		</form>
	</div>
</template>

<script>
import tags from "@dsi/components/tags.vue";
import Descriptors from "@/common/components/descriptors.vue";

export default {
	props : ["channels", "status", "diffusion", "products", "empr"],
	components: {
		tags,
		Descriptors
	},
	data: function () {
		return {
			dataInProgress: {},
			activeProgress: false
		}
	},
	created: async function() {
		this.diffusion.diffusionProducts = this.diffusion.diffusionProducts ? this.diffusion.diffusionProducts : [];
        if (this.diffusion.numStatus === 0) {
            this.diffusion.numStatus = "";
        }
        if (!this.diffusion.settings.nb_history_saved) {
            this.$set(this.diffusion.settings, "nb_history_saved", 1);
        }

		if(! this.diffusion.settings.opacName) {
			this.$set(this.diffusion.settings, "opacName", "");
		}

		if(! this.diffusion.settings.opacVisibility) {
			this.$set(this.diffusion.settings, "opacVisibility", false);
		}

		if(! this.diffusion.settings.opacVisibilityCateg) {
			this.$set(this.diffusion.settings, "opacVisibilityCateg", []);
		}

		if(! this.diffusion.settings.opacVisibilityGroups) {
			this.$set(this.diffusion.settings, "opacVisibilityGroups", []);
		}

		await this.fetchDataInProgressDiffusion();
	},
	computed : {
        // lastDiffusionHistoryDate: function() {
        //     if(! this.diffusion.diffusionHistory || ! this.diffusion.diffusionHistory.length) {
        //         return this.messages.get('dsi', 'diffusion_never_send');
        //     }
		// 	let lastHistorySend = this.diffusion.diffusionHistory.reverse().find((history) => history.state == 3);
		// 	if(! lastHistorySend) {
		// 		return this.messages.get('dsi', 'diffusion_never_send');
		// 	}
        //     let date = new Date(lastHistorySend.date);

        //     return date.toLocaleDateString([], { hour: "2-digit", minute: "2-digit", second: "2-digit" });
        // },
		canManuallySend: function() {
			return this.diffusion.automatic && this.diffusion.channel.id;
		},
		manuallySendIsInProgress: function() {
			return this.dataInProgress.numDiffusionsHistory;
		},
		manuallySendProgressPercentage: function() {
			if (this.dataInProgress.totalElements === 0) {
				return 0; // Éviter la division par zéro si totalElements est 0
			}
			return Math.round(((this.dataInProgress.totalElements - this.dataInProgress.remainingElements) / this.dataInProgress.totalElements) * 100);
		},
		manuallySendLabel: function() {
			if(this.manuallySendIsInProgress && !this.activeProgress) {
				return this.messages.get('dsi', 'dsi_send_diffusion_manually_in_progress_pending');
			}

			if(this.manuallySendIsInProgress && this.activeProgress) {
				return this.messages.get('dsi', 'dsi_send_diffusion_manually_in_progress').replace('%s', this.manuallySendProgressPercentage);
			}

			return this.messages.get('dsi', 'dsi_send_diffusion_manually');
		},
		manuallySendTitle: function() {
			if(this.manuallySendIsInProgress) {
				return this.messages.get('dsi', 'dsi_send_diffusion_manually_in_progress_title').replace('%s', this.manuallySendProgressPercentage);
			} else {
				return "";
			}
		},
		canBypassSendQueue: function() {
			if(!this.diffusion.numChannel) {
				return false;
			}

			let channelSelected = this.channels.find(channel => channel.id == this.diffusion.channel.type);

			if(!channelSelected) {
				return false;
			}

			return channelSelected.bypassSendQueue == "1";
		},
	},
	methods: {
		getProductName: function(id) {
			return this.products.find((product) => product.id == id);
		},
		cancel: function() {
			document.location = "./dsi.php?categ=diffusions";
		},
		submit: async function() {
			if(! this.diffusion.settings.opacName) {
				this.$set(this.diffusion.settings, "opacName", this.diffusion.name);
			}

			let response = await this.ws.post('diffusions', 'save', this.diffusion);
			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				document.location = "dsi.php?categ=diffusions&action=edit&id=" + response.id;
			}
		},
		del: async function() {
			if (confirm(this.messages.get('dsi', 'confirm_del'))) {
				let response = await this.ws.post("diffusions", 'delete', this.diffusion);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					document.location = "./dsi.php?categ=diffusions";
				}
			}
		},
		fetchDataInProgressDiffusion: async function() {
			this.dataInProgress = await this.ws.get('diffusions', `getDataInProgressDiffusion/${this.diffusion.id}`);
		},
		sendDiffusionManually : async function() {
			if (this.canManuallySend && confirm(this.messages.get('dsi', 'dsi_send_diffusion_manually_alert'))) {
				this.$set(this, "activeProgress", true);

				let options = {
					method: "GET",
					cache: 'no-cache',
				};

				if(this.canBypassSendQueue) {
					let url = `diffusions/${this.diffusion.id}/send/0`;
	
					let response = await fetch(this.$root.url_webservice + url, options);
					let result = await response.json();
					if(!result.error) {
						this.notif.info(this.messages.get('dsi', 'dsi_diffusion_sent'));
					} else {
						this.notif.error(this.messages.get('dsi', result.errorMessage));
					}

				} else {
					const totalPackets = Math.ceil(this.dataInProgress.remainingElements / this.dataInProgress.nbPerPass);
					for (let i = 0; i < totalPackets; i++) {

						let url = `diffusions/${this.diffusion.id}/send/${this.dataInProgress.numDiffusionsHistory}`;
	
						let response = await fetch(this.$root.url_webservice + url, options);
						let result = await response.json();
	
						if(!result.error) {
							this.$set(this.dataInProgress, "remainingElements", this.dataInProgress.remainingElements - this.dataInProgress.nbPerPass);
							this.$set(this.dataInProgress, "numDiffusionsHistory", result.idHistory);
	
							if(this.dataInProgress.remainingElements <= 0) {
								this.notif.info(this.messages.get('dsi', 'dsi_diffusion_sent'));
							}
	
						} else {
							this.notif.error(this.messages.get('dsi', result.errorMessage));
						}
					}
				}

				this.$set(this, "activeProgress", false);
				await this.fetchDataInProgressDiffusion();
			}
		},
		updateDescriptors: function (descriptors) {
			this.diffusion.descriptors = descriptors;
		}
	}
}
</script>