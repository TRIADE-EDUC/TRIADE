<template>
	<div id="organizationList">
		<table>
			<tr>
				<th>{{ messages.get("payment", "admin_oraganization_name") }}</th>
			</tr>
			<tr v-for="(organization, index) in organizationlist" :key='index' style='cursor: pointer'>
				<td @click="editOrganisation(organization.id)">
					{{ organization.name }}
				</td>
			</tr>
		</table>
		<div class='row'>
			<input v-if="organizationlistavaible.length" @click="newOrganisation" class="bouton" type="button"
				:value="messages.get('payment', 'admin_payment_oarganization_add')" />
		</div>
	</div>
</template>

<script>

export default {
	props: ["organizationlist", "organizationlistavaible"],

	data: function () {
		return {
		}
	},
	computed: {
	},
	methods: {
		newOrganisation: function () {
			document.location = './admin.php?categ=finance&sub=organization_account&action=add';
		},

		editOrganisation: function (id) {
			document.location = './admin.php?categ=finance&sub=organization_account&action=edit&id=' + id;
		},
	},
	components: {
	}
}
</script>