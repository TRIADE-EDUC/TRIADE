<template>
	<div>
		<h2>{{ data.messages.name }}</h2>
		<p>{{ data.messages.description }}</p>

		<form class="form-admin thumbnail-small-form" action="" method="POST" @submit.prevent="add">
			<div class="form-contenu">
				<h3>{{ data.messages.form_parameters }}</h3>

				<div class="row group-form">
					<label class="etiquette" for="lvl_bibli">{{ data.messages.lvl_bibli }}</label>
					<select id="lvl_bibli" name="lvl_bibli" v-model="selected.nivbiblio" required>
						<option v-for="(label, code) in nivbiblio" :value="code" :key="code">
							{{ label }}
						</option>
					</select>
				</div>

				<div class="row  group-form">
					<label class="etiquette" for="doc_type">{{ data.messages.doc_type }}</label>
					<div class="group-form-action">
						<select id="doc_type" name="doc_type" v-model="selected.typedoc">
							<option v-for="(label, code) in typedoclist" :value="code" :key="code">
								{{ label }}
							</option>
						</select>
						<button class="bouton" type="submit">
							{{ messages.get("common", "more") }}
						</button>
					</div>
				</div>
			</div>
		</form>

		<form class="form-admin" action="" method="POST" @submit.prevent="submit">
			<div class="form-contenu">
				<div class="row" v-for="(value, index) in values">
					<div class="colonne3">
						<label class="etiquette"
							:for="`value${index}`">{{ makeLabel(value.typedoc, value.nivbiblio) }}</label>
					</div>
					<div class="colonne_suite">
						<input class="saisie-20em" type="text" :name="`value${index}`" :id="`value${index}`"
							v-model="values[index].value" required>
						<button class="bouton" type="button" @click="remove(index)" v-if="value.nivbiblio"
							:title="messages.get('common', 'remove')">
							{{ messages.get("common", "remove_short") }}
						</button>
					</div>
				</div>
			</div>

			<div class="form-contenu row">
				<button class="bouton btnCancel" type="button" @click="cancel">
					{{ messages.get("common", "cancel") }}
				</button>
				<button class="bouton" type="submit">
					{{ messages.get("common", "submit") }}
				</button>
			</div>
		</form>
	</div>
</template>

<script>
export default {
	props: ["data"],
	data: function () {
		return {
			hover: null,
			selected: {
				typedoc: "",
				nivbiblio: ""
			},
			typedoc: {},
			nivbiblio: {},
			values: [
				{
					typedoc: "",
					nivbiblio: "",
					value: ""
				}
			]
		}
	},
	created: function () {
		this.typedoc = this.helper.cloneObject(this.data.parameters.typedoc)
		this.nivbiblio = this.helper.cloneObject(this.data.parameters.nivbiblio)
		this.values = this.helper.cloneObject(this.data.parameters.values)
	},
	computed: {
		typedoclist: function () {
			const matches = this.values.filter(value => value.nivbiblio == this.selected.nivbiblio);
			const typedocUsed = matches.map(value => value.typedoc);
			if (!typedocUsed.length) {
				return this.typedoc;
			}
			const list = Object.entries(this.typedoc).filter(value => !typedocUsed.includes(value[0]));
			return Object.fromEntries(list);
		}
	},
	methods: {
		cancel: function () {
			this.$emit('cancel');
		},
		makeLabel: function (selectedTypedoc, selectedNivbiblio) {
			if (!selectedTypedoc && !selectedNivbiblio) {
				return this.data.messages.default;
			}
			if (!selectedTypedoc) {
				return this.data.parameters.nivbiblio[selectedNivbiblio];
			}
			return this.data.parameters.nivbiblio[selectedNivbiblio] + " / " + this.data.parameters.typedoc[selectedTypedoc] + " : ";
		},
		add: function () {
			this.values.push({
				...this.selected,
				value: ""
			})
		},
		remove: function (index) {
			this.values.splice(index, 1);
		},
		submit: async function () {
			let response = await this.ws.post(this.data.entityType, "noimage/save", { values: this.values });
			if (response.error) {
				if (response.errorMessage) {
					console.error(response.errorMessage);
				}
				this.notif.error(this.messages.get("common", "failed_save"));
			} else {
				this.notif.info(this.messages.get("common", "success_save"));
			}
		}
	}
}
</script>