<template>
	<form id="form-subscriber" name="form-subscriber" class="dsi-form-subscriber">
		<div class="dsi-form-group" >
			<label class="etiquette" for="name-subscriber">{{ messages.get('dsi', 'subscriber_name') }}</label>
			<div class="dsi-form-group-content">
				<input type="text" name="name-subscriber" id="name-subscriber" v-model="clonedSubscriber.name" required />
			</div>
		</div>
		<div class="dsi-form-group" v-for="(settingValue, settingKey) in clonedSubscriber.settings" :key="settingKey">
			<label v-if="requirements[settingKey] !== undefined" class="etiquette" for="email-subscriber">{{ messages.get('dsi', requirements[settingKey].input_label) }}</label>
			<div v-if="requirements[settingKey] !== undefined" class="dsi-form-group-content">
				<input :type="requirements[settingKey].input_type" v-model="clonedSubscriber.settings[settingKey]" />
			</div>
		</div>

		<div class='row dsi-br'>
			<div class="left">
				<input type="button" @click.prevent="submitSubscriber" class="bouton" :value="messages.get('common', 'submit')">
			</div>
		</div>
	</form>
</template>
<script>
export default {
	props : ['idEntity', 'subscriber', "requirements", "fromPending"],
	created : async function() {
		this.getSubscriber();
	},
	watch : {
		requirements : function() {
			this.getSubscriber();
		},
		subscriber : function() {
			this.getSubscriber();
		}
	},
	data : function() {
		return {
			clonedSubscriber : {}
		}
	},
	methods : {
		submitSubscriber : async function()
		{
			for(let i in this.Const.subscriberlist.subscriberForeignKeys) {
				if(typeof this.clonedSubscriber[this.Const.subscriberlist.subscriberForeignKeys[i]] !== 'undefined') {
					this.clonedSubscriber[this.Const.subscriberlist.subscriberForeignKeys[i]] = this.idEntity;
				}
			}
			if(! this.clonedSubscriber.type) {
				this.$set(this.clonedSubscriber, "type", this.Const.subscriberlist.subscriberTypes.manualAdd);
			}

			if(this.fromPending) {
				this.$root.$emit("importSubscriber", [this.clonedSubscriber]);
				this.getSubscriber()
				return;
			}

			let response = await this.ws.post('subscribers', this.$root.categ + '/' + this.idEntity + '/save', this.clonedSubscriber);
			if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
				if(this.subscriber.id) {
					this.$root.$emit("editSubscriber", response);
				} else {
					this.$root.$emit("addSubscriber", response);
				}
				this.notif.info(this.messages.get('dsi', "dsi_subscriber_created"));
			}
		},
		getSubscriber : function() {
			this.clonedSubscriber = JSON.parse(JSON.stringify(this.subscriber));
			for(let requirement in this.requirements) {
				if(this.clonedSubscriber.settings[requirement] === undefined) {
					this.$set(this.clonedSubscriber.settings, requirement, "");
				}
			}
		}
	}
}
</script>