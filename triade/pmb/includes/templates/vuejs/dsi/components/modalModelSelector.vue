<template>
	<div>
		<modalModel v-if="showModal" @close="$emit('close')" @keyup.enter.stop @keydown.enter.stop>
			<h3 slot="header">{{ messages.get('dsi', 'create_model') }}</h3>
			<div slot="body">
				<div class="dsi-form-group">
				<label class="etiquette" for="model-name">{{ messages.get('dsi', 'channel_form_name') }}</label>
					<div class="dsi-form-group-content">
						<input type="text" class="dsi-model-name" id="model-name" name="model-name" v-model="entity.name" v-focus required>
					</div>
				</div>
			</div>
			<div slot="footer">
				<input v-if="idForm" name="submit_model_from_modal" type="button" class="bouton" @click="submitForm($event)" :value="messages.get('common', 'submit')">
				<input v-else name="submit_model_from_modal" type="submit" class="bouton" :value="messages.get('common', 'submit')">
			</div>
		</modalModel>
	</div>
</template>

<script>
	import modalModel from "./modal.vue";

	export default {
		props: ["showModal", "entity", "idForm"],
		components: {
			modalModel
		},
		methods: {
			submitForm: function(e) {
				const excludeElements = this.Const.modelModalExcludeElements;

				for (const el of document.getElementById(this.idForm).querySelectorAll("[required]")) {
					if (!el.reportValidity() && excludeElements.findIndex(element => element === el.name) == -1) {
						return;
					}
				}

				this.$emit('submit', e.target)
			}
		}
	}
</script>